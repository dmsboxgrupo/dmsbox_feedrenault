<?php
require 'dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$fileURL = sprintf("%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
$baseURL = substr($fileURL, 0, -18);


$img = $_POST['img'];
$nome = $_POST['nome'];
$cargo = $_POST['cargo'];
$whatsapp = $_POST['whatsapp'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$localizacao = $_POST['localizacao'];
$concessionaria = $_POST['concessionaria'];
$leftlogo = $_POST['leftlogo'];
$leftDados = $_POST['leftDados'];
$topDados = $_POST['topDados'];

$html = '<!DOCTYPE html>
          <html>
            <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
              <link rel="stylesheet" href="'. $baseURL . 'assets/css/dashboard/cartao.css" type="text/css"/>
            </head>
            <body>
              <div class="card-content print">
                <div class="img-perfil">
                  <img src="'. $baseURL . $img .'">
                </div>
                <div class="bg-card">
                  <img src="'. $baseURL . 'assets/images/bg_cartao_pdf.jpg">
                </div>

                <div class="nome">
                  <h3><span>'. $nome .'</span></h3>
                  <p>'. $cargo .'</p>
                </div>

                <div class="itens">
                  <div class="item">
                    <a href="'. $whatsapp .'" id="linkwhatsapp" class="hld">
                        <div class="ico"><img src="'. $baseURL . 'assets/images/ico_inp_whatsapp.svg"></div>
                        <div class="txt">WhatsApp</div>
                    </a>
                  </div>
                  <div class="item">
                    <a href="'. $telefone .'" id="linktelefone" class="hld">
                        <div class="ico"><img src="'. $baseURL . 'assets/images/ico_inp_telefone.svg"></div>
                        <div class="txt">telefone</div>
                    </a>
                  </div>
                  <div class="item">
                    <a href="'. $email .'" id="linkemail" class="hld">
                        <div class="ico"><img src="'. $baseURL . 'assets/images/ico_inp_email.svg"></div>
                        <div class="txt">e-mail</div>
                    </a>
                  </div>
                  <div class="item">
                    <a href="'. $localizacao .'" id="linklocalizacao" class="hld">
                        <div class="ico"><img src="'. $baseURL . 'assets/images/ico_inp_localizacao.svg"></div>
                        <div class="txt">localização</div>
                    </a>
                  </div>
                </div>



                <div class="assinatura">   
                  <div class="logo" style="left: '. $leftlogo .'%">
                    <img src="'. $baseURL . 'assets/images/logo_renault.svg">        
                  </div>     
                  <div class="dados" style="left: '. $leftDados .'%; top: '. $topDados .'px">             
                    <div class="renault">Renault</div>                
                    <div class="concessionaria">'. $concessionaria .'</div>            
                  </div>        
                </div>

              </div>    
            </body>
          </html>';

$fonts_folder = $_SERVER['DOCUMENT_ROOT']."/newfeed/assets/fonts/";
$tmp_folder = $fonts_folder ."/temp";


$options = new Options();
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isFontSubsettingEnabled', true);
$options->set('defaultMediaType', 'all');
$options->set('fontDir', $fonts_folder);
$options->set('fontCache', $tmp_folder);
$options->set('tempDir', $tmp_folder);
$options->set('chroot', $fonts_folder);
$options->set('dpi', '72');

$customPaper = array(0,0,1080,1920);
$nomePDF = md5(uniqid(rand(), true));

$dompdf = new Dompdf($options);
$dompdf->set_paper($customPaper);
$dompdf->load_html($html);
$dompdf->render();

$output = $dompdf->output();
$file = '../download/pdf/'. $nomePDF .'.pdf';
file_put_contents($file, $output);

echo json_encode(array('name'=>$nomePDF, 'folder'=>$fonts_folder));