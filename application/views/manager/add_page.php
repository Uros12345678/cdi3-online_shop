<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Add page</h1>

                <?=validation_errors()?>
                <?=form_open_multipart(base_url('manager/add_page'))?>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?=set_value('title')?>">
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea class="form-control" id="content" name="content"><?=set_value('content')?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Add new</button>
                <?=form_close()?>
            </div><!-- .col-12 -->
        </div><!-- .row -->
    </div>

    
<script src="//cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'content' );
</script>