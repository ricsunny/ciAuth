<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Change Password</title>
        <link href="<?php echo base_url(); ?>includes/bootstrap-combined.min.css" rel="stylesheet" >
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    </head>
    <body>
        <form action="<?php echo base_url(); ?>welcome/update_password" method="post" >
            <div class="modal" id="password_modal">

                <div class="modal-header">
                    <h3>Change Password <span class="extra-title muted"></span></h3>
                    <script>
// assumes you're using jQuery
                        $(document).ready(function() {
                            $('.confirm-div').hide();
<?php if ($this->session->flashdata('message')) { ?>
                                $('.confirm-div').html('<?php echo $this->session->flashdata('message'); ?>').show();
                            });
<?php } ?>
                    </script>
                    <div class="confirm-div"></div>
                </div>
                <div class="modal-body form-horizontal">
                    <input name="hash_email" type="hidden" value="<?php echo @$hash_email; ?>">  
                    <div class="control-group">
                        <label for="new_password" class="control-label">New Password</label>
                        <div class="controls">
                            <input name="new_password" type="password" required>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="confirm_password" class="control-label">Confirm Password</label>
                        <div class="controls">
                            <input name="confirm_password" type="password" required>
                        </div>
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn" data-dismiss="modal" aria-hidden="true">Reset</button>
                    <button type="submit" class="btn btn-primary" id="password_modal_save">Save changes</button>
                </div>
            </div>
        </form>
    </body>
</html>