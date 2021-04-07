<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Edit page</h1>

                <?=validation_errors()?>
                <?=form_open_multipart(base_url('manager/edit_page/' . $item->id))?>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?=set_value('title', $item->title)?>">
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea class="form-control" id="content" name="content"><?=set_value('content', $item->content)?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                <?=form_close()?>
            </div><!-- .col-12 -->
        </div><!-- .row -->
    </div>

    
<script src="//cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'content' );
</script>