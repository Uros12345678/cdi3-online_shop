<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Categories <a class="btn btn-success" href="<?=base_url('manager/add_category')?>">New category</a></h1>
            </div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                        <tr>
                            <th scope="row"><?=$item->id?></th>
                            <td><?=$item->title?></td>
                            <td>
                                <a class="btn btn-danger delete" href="<?=base_url('manager/delete_category/' . $item->id)?>" title="Delete"><span class="oi oi-trash"></span></a>
                                <a class="btn btn-primary" href="<?=base_url('manager/edit_category/' . $item->id)?>" title="Edit"><span class="oi oi-pencil"></span></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-12 m-auto">
                <?=$pagination?>
            </div>
        </div>
    </div>