<div class="form-group" id="to-mail-group">
	<label for="">Send this return label To :</label> (<small>Separate each by "<b>;</b>"</small>)
	<input type="text" class="form-control" name="to" id="return-label-to-mail">
</div>
<div class="form-group">
	<label for="">Return Label :</label>
	<button type="button" class="btn btn-xs btn-danger"><span class="fa fa-file-pdf-o"></span> <?php echo $current_file;?></button>
</div>
<input type="hidden" name="orderId" value="<?php echo $order->id; ?>"/>