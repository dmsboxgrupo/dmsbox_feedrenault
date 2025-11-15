<?php if ($this->logged) { ?>
			</div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer"> © <?php echo date("Y"); ?> Renault </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
<?php } ?>
    </section>
	<!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- Bootstrap tether Core JavaScript -->
	<?php $this->script('admin/template/assets/plugins/popper/popper.min.js'); ?>
	<?php $this->script('admin/template/assets/plugins/bootstrap/js/bootstrap.min.js'); ?>
    <!-- slimscrollbar scrollbar JavaScript -->
	<?php $this->script('admin/template/js/jquery.slimscroll.js'); ?>
    <!--Wave Effects -->
	<?php $this->script('admin/template/js/waves.js'); ?>
    <!--Menu sidebar -->
	<?php $this->script('admin/template/js/sidebarmenu.js'); ?>
    <!--stickey kit -->
	<?php $this->script('admin/template/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js'); ?>
	<?php $this->script('admin/template/assets/plugins/sparkline/jquery.sparkline.min.js'); ?>
    <!--Custom JavaScript -->
	<?php $this->script('admin/template/js/custom.min.js'); ?>    
    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
	<?php $this->script('admin/template/assets/plugins/styleswitcher/jQuery.style.switcher.js'); ?>
	<!-- Plugin -->
	<?php $this->plugins_format('js'); ?>
</body>
</html>