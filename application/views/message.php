<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php if(@$title){echo @$title; }else{ echo "Change Password";} ?></title>
        <link href="<?php echo base_url(); ?>includes/bootstrap-combined.min.css" rel="stylesheet" >
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    </head>
    <body>
        <form action="<?php echo base_url(); ?>welcome/update_password" method="post" >
            <div class="modal" id="password_modal">

                <div class="modal-header">
                    <h3><?php if(@$title){echo @$title; }else{ echo "Change Password";} ?> <span class="extra-title muted"></span></h3>
                </div>
                <div class="modal-body form-horizontal">
                    <?php echo @$message; ?>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </form>
    </body>
</html>