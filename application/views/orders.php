<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
	<div class="py-5 text-center">
		<h2>My Orders</h2>
	</div>
	<div class="row">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Date</th>
					<th scope="col">Price</th>
					<th scope="col">Satus</th>
					<th scope="col">Detail</th>
					<th scope="col">Purchace</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($items as $item): ?>
					<tr>
						<th scope="row"><?=$item->id?></th>
						<td><?=$item->created?></td>
						<td><?=number_format($item->price, 2)?> $</td>
						<td><?=$item->status?></td>
						<td>
							<button class="btn btn-primary order-detail" data-id="<?=$item->id?>" data-toggle="modal" data-target="#orderModal">
								<span class="oi oi-eye"></span>
							</button>
						</td>
						<td>
							<a href="<?= base_url('home/purchaces/' . $item->id)?>" class="btn btn-warning">Click for Payment</a>
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


<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="orderModal">Order detail</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Loading...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
