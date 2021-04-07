<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Products</h1>
        </div>
    </div>
    <div class="row">
        <?php if(count($items) == 0): ?>
        <div class="col-6">
            <div class="alert alert-danger">Product not found</div>
        </div>
        <?php endif;?>
        <?php foreach($items as $item): ?>
            <div class="col-4">
                <div class="card">
                    <img src="<?=base_url('uploads/'.$item->image)?>" class="card-img-top" alt="<?=$item->title?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title"><?=$item->title?></h5>
                                <p class="card-text"><?=$item->price?>$</p>
                            </div>
                            <div class="col-md-3">
                                <a href="<?=base_url('add/' . $item->id)?>" class="btn btn-primary btn-lg" title="Cart">
                                    <span class="oi oi-cart"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div><!-- .col-4 -->
        <?php endforeach; ?>
    </div>
    <div class="row mt-3">
        <div class="col-12 m-auto">
            <?=$pagination?>
        </div>
    </div>
</div>
