<?php

require_once 'config.php';

class Socials {
  protected $twitterToken;
  protected $youtubeToken;
  protected $facebookToken;
  protected $cacheTimeout = 0; // 14400 = 24h
  protected $conn;
  protected $now;
  protected $returnLimit;
  protected $videoHandlerCallback;

  /**
   *
   * @param string $twitterToken Twitter Token
   * @param string $youtubeToken Youtube Token
   * @param string $facebookToken Facebook Token
   * @param int $cacheTimeout Duração do cache em segundos. Default = 14400 (4h)
   * @param int $returnLimit Máximo de registros retornados por consulta. Default = 50
  */
  public function __construct($twitterToken, $youtubeToken, $facebookToken, $cacheTimeout = 14400, $returnLimit = 50, callable $videoHandlerCallback = null) {
    $this->twitterToken = $twitterToken;
    $this->youtubeToken = $youtubeToken;
    $this->facebookToken = $facebookToken;
    $this->cacheTimeout = $cacheTimeout;
    $this->returnLimit = $returnLimit;
    $this->videoHandlerCallback = ($videoHandlerCallback == null ? 'Socials::defaultVideoHandlerCallback' : $videoHandlerCallback);
    date_default_timezone_set("America/Sao_Paulo");
    $this->now = date("Y-m-d H:i:s");
    $this->initDB();
  }

  public function defaultVideoHandlerCallback($videoArray, $default) {
    $maxbitrate = 0;
    $video = $default;
    foreach ($videoArray as $el) {
      if (isset($el["bitrate"]) && intval($el["bitrate"]) > $maxbitrate) {
        $maxbitrate = intval($el["bitrate"]);
        $video = $el["url"];
      }
    }
    return $video;
  }

  protected function request($url, $header = array()) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);     curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $request = curl_exec($curl);
    if (!$request) { echo(curl_error($curl)); }
    curl_close($curl);
    $request = json_decode($request, true);
    return $request;
  }

  protected function initDB() {
    $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $this->conn->set_charset('utf8mb4');
    if ($this->conn->connect_error) throw new Exception("Connection failed: " . $this->conn->connect_error);
    $return = $this->conn->query("CREATE Table If Not Exists socials_data (
      id Int Unsigned Not Null Auto_Increment Primary Key,
      social VarChar(50), 
      user VarChar(50), 
      pid VarChar(50), 
      caption VarChar(2000) Collate 'utf8mb4_unicode_ci', 
      images VarChar(5000), 
      video VarChar(2000), 
      link VarChar(5000), 
      created DateTime, 
      Unique Index id_social_user_pid (social, user, pid))");
    if (!$return) throw new Exception("Error executing SQL " . mysqli_error($this->conn));
    $return = $this->conn->query("CREATE Table If Not Exists socials_date (
      id Int Unsigned Not Null Auto_Increment Primary Key,
      social VarChar(200), 
      user VarChar(50),
      what VarChar(50),
      created DateTime,
      Unique Index id_social_user_what (social, user, what))");
    if (!$return) throw new Exception("Error executing SQL " . mysqli_error($this->conn));
  }

  protected function readDate($social, $user, $type) {
    $result = null;
    $stmt = $this->conn->prepare("SELECT created From socials_date Where Social=? And User=? And What=?");
    $stmt->bind_param("sss", $social, $user, $type);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    return $result;
  }

  protected function shouldWhat($social, $user, $what, $timeout) {
    $then = $this->readDate($social, $user, $what);
    return ($then == null || strtotime($this->now) - strtotime($then) > $timeout);
  }

  protected function shouldUpdate($social, $user) {
    return $this->shouldWhat($social, $user, "update", $this->cacheTimeout);
  }

  protected function shouldRefresh($social, $user) {
    return $this->shouldWhat($social, $user, "refresh", 4320000);
  }

  protected function saveDate($social, $user, $what, $created) {
    $stmt = $this->conn->prepare("REPLACE Into socials_date (social, user, what, created) Values (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $social, $user, $what, $created);
    $stmt->execute();
    $stmt->close();
  }

  protected function saveUpdate($social, $user) {
    $this->saveDate($social, $user, "update", $this->now);
  }

  protected function saveRefresh($social, $user) {
    $this->saveDate($social, $user, "refresh", $this->now);
  }

  protected function transformData($data) {
    $result = array();
    foreach ($data as $el) {
      $result[] = array(
        'social' => $el["social"],
        'user' => $el["user"],
        'id' => $el["id"],
        'uid' => $el["pid"],
        'message' => $el["caption"],
        'images' => json_decode($el["images"]), 
        'video' => $el["video"],
        'link' => $el["link"],
        'time' => $el["created"]
      );
    }
    return $result;
  }

  public function readData($social, $user, $limit = 0) {
    if ($limit == 0) $limit = $this->returnLimit;
    $data = null;
    $stmt = $this->conn->prepare("SELECT * From socials_data Where social=? And user=? Order By created Desc Limit $limit");
    $stmt->bind_param("ss", $social, $user);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $this->transformData($data);
  }

  protected function saveData($social, $user, $id, $caption, $images, $video, $link, $created) {
    $images_json = json_encode($images);
    $date = date("Y-m-d H:i:s", strtotime($created));
    $stmt = $this->conn->prepare("REPLACE Into socials_data (social, user, pid, caption, images, video, link, created) Values (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $social, $user, $id, $caption, $images_json, $video, $link, $date);
    $stmt->execute();
    $stmt->close();
  }

  protected function addInstagramData($social, $user, $data) {
    foreach ($data as $el) {
      $images = array(); $video = null;
      $type = $el["media_type"];
      if ($type == "CAROUSEL_ALBUM" && isset($el["children"]) && isset($el["children"]["data"])) {
        foreach ($el["children"]["data"] as $key2 => $el2) {
          $images[] = $el2["media_url"];
        }
      } else if ($type == "IMAGE") {
        $images[] = $el["media_url"];
      } else { // VIDEO
        $video = $el["media_url"];
      }
      $caption = "";
      if (isset($el["caption"])) $caption = $el["caption"];
      $this->saveData($social, $user, $el["id"], $caption, $images, $video, $el["permalink"], $el["timestamp"]);
    }
  }

  protected function getInstagram($user, $type) {
    $social = "instagram" . $type;
    if ($this->shouldUpdate($social, $user)) {
      $result = $this->request("https://graph.facebook.com/v10.0/" . $user . "/" . $type . "?limit=50&fields=permalink,media_type,media_url,timestamp,caption,thumbnail_url,children{media_url,thumbnail_url}&access_token=" . $this->facebookToken);
      if (!$result || !isset($result["data"])) {
        throw new Exception("Erro ao recuperar feed. Verifique o token " . $social);
      }
      $this->addInstagramData($social, $user, $result["data"]);
      $this->saveUpdate($social, $user);
    }
    return $this->readData($social, $user);
  }

  public function getInstagramFeed($user) {
    return $this->getInstagram($user, "media");
  }

  public function getInstagramStories($user) {
    return $this->getInstagram($user, "stories");
  }

  protected function addTwitterData($social, $user, $data) {
    foreach ($data as $el) {
      $images = array(); $video = null;
      if (isset($el["extended_entities"]) && isset($el["extended_entities"]["media"])) {
        foreach ($el["extended_entities"]["media"] as $key2 => $el2) {
          $images[] = $el2["media_url_https"];
          if (isset($el2["video_info"]) && isset($el2["video_info"]["variants"])) {
            $video = call_user_func($this->videoHandlerCallback, $el2["video_info"]["variants"], $el2["expanded_url"]);
          }
        }
      }
      $link = "https://twitter.com/" . $user . "/status/" . $el["id_str"];
      $this->saveData($social, $user, $el["id_str"], $el["full_text"], $images, $video, $link, $el["created_at"]);
    }
  }

  public function getTwitterFeed($user) {
    $social = "twitter";
    if ($this->shouldUpdate($social, $user)) {
      $result = $this->request("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" .
        $user . "&count=200&include_rts=false&exclude_replies=true&tweet_mode=extended", array("Authorization: Bearer " . 
        $this->twitterToken));
      if (!$result) throw new Exception("Erro ao recuperar feed. Verifique o token " . $social);
      $this->addTwitterData($social, $user, $result);
      $this->saveUpdate($social, $user);
    }
    return $this->readData($social, $user);
  }

  protected function addYoutubeData($social, $user, $data) {
    foreach ($data as $el) {
      $title = null; $images = array(); $video = null; $time = null;
      if (isset($el["snippet"])) {
        $title = $el["snippet"]["title"];
        if (isset($el["snippet"]["thumbnails"])) {
          if (isset($el["snippet"]["thumbnails"]["maxres"])) {
            $images[] = $el["snippet"]["thumbnails"]["maxres"]["url"];
          } else {
            $images[] = $el["snippet"]["thumbnails"]["high"]["url"];
          }
        }
      }
      if (isset($el["contentDetails"])) {
        $video = "https://www.youtube.com/watch?v=" . $el["contentDetails"]["videoId"];
        $time = $el["contentDetails"]["videoPublishedAt"];
      }
      $this->saveData($social, $user, $el["id"], $title, $images, $video, $video, $time);
    }
  }

  public function getYoutubeFeed($user) {
    $social = "youtube";
    if ($this->shouldUpdate($social, $user)) {
      $playlist = $user;
      if (strlen($playlist) == 22) {
        $playlist = "UU" . $playlist;
      } else if (strlen($playlist) == 24) {
        $playlist[0] = "U"; $playlist[1] = "U"; // Garante que começa com UU = User Uploads
      } else {
        // Recupera ID a partir de nome do usuario
        $result = $this->request("https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=" . 
          $user . "&key=" . $this->youtubeToken);
        if (!$result || !isset($result["items"])) throw new Exception("Erro ao recuperar feed. Verifique o token " . $social);
        $playlist = $result["items"][0]["contentDetails"]["relatedPlaylists"]["uploads"];
      }
      $result = $this->request("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2CcontentDetails&maxResults=50&playlistId=" . 
        $playlist . "&key=" . $this->youtubeToken);
      if (!$result || !isset($result["items"])) throw new Exception("Erro ao recuperar feed. Verifique o token " . $social);
      $this->addYoutubeData($social, $user, $result["items"]);
      $this->saveUpdate($social, $user);
    }
    return $this->readData($social, $user);
  }

  protected function addFacebookData($social, $user, $data) {
    foreach ($data as $el) {
      $title = null; $images = array(); $video = null;
      if (isset($el["attachments"]) && isset($el["attachments"]["data"])) {
        foreach ($el["attachments"]["data"] as $el2) {
          if (isset($el2["type"])) {
            $type = $el2["type"];
            if (isset($el2["description"])) {
              $title = $el2["description"];
            } else if (isset($el2["title"])) {
              $title = $el2["title"];
            }
            if ($type == "album") {
              if (isset($el2["subattachments"]) && isset($el2["subattachments"]["data"])) {
                foreach ($el2["subattachments"]["data"] as $el3) {
                  if (isset($el3["media"]) && isset($el3["media"]["image"]) && isset($el3["media"]["image"]["src"])) {
                    $images[] = $el3["media"]["image"]["src"];
                  }
                }
              }
            } else {
              if (isset($el2["media"]) && isset($el2["media"]["image"]) && isset($el2["media"]["image"]["src"])) {
                $images[] = $el2["media"]["image"]["src"];
              }
            }
            if (substr($type, 0, 5) == "video") {
              if (isset($el2["media"]) && isset($el2["media"]["source"])) {
                $video = $el2["media"]["source"];
              }
            }
          }
        }
      }
      if (isset($el["message"])) {
        $title = $el["message"];
      }
      if (isset($el["status_type"]) && $el["status_type"] != "wall_post") {
        $this->saveData($social, $user, $el["id"], $title, $images, $video, $el["permalink_url"], $el["created_time"]);
      }
    }
  }

  public function getFacebookFeed($user) {
    $social = "facebook";
    if ($this->shouldUpdate($social, $user)) {
      $result = $this->request("https://graph.facebook.com/v10.0/$user/feed?fields=permalink_url,message,attachments,created_time,updated_time,status_type&limit=100&access_token=" . $this->facebookToken);
      if (!$result || !isset($result["data"])) throw new Exception("Erro ao recuperar feed. Verifique o token " . $social);
      $this->addFacebookData($social, $user, $result["data"]);
      $this->saveUpdate($social, $user);
    }
    return $this->readData($social, $user);
  }
}

?>