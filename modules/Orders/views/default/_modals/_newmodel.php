<?php 
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use app\models\Category;
	use app\models\Manufacturer;
	use app\models\Vendor;
?>
<style>
	#newmodel-form label
	{
		display: inline;
	}
</style>
<!-- Modal -->
<div class="modal fade" id="newModel" tabindex="-1" role="dialog" aria-labelledby="newModelLabel" style="z-index:10000">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> Add New Model</h4>
	  </div>
		<div class="alert alert-success fade in" id="newmodel-msg" style="display:none;"></div>
		<?php $form = ActiveForm::begin(['options' => ['action'=>['/orders/addmodel'], 'id'=>'newmodel-form', 'class'=>'form-group form-group-sm']]); ?>
			<div class="modal-body">
				<div class="container">
					<div id="wizard" class="form_wizard wizard_horizontal">
						<ul class="wizard_steps">
							<li>
								<a href="#step-1">
									<span class="step_no">1</span>
									<span class="step_descr">
							Basic Options<br />
							<small>All basic options</small>
						</span>
								</a>
							</li>
							<li>
								<a href="#step-2">
									<span class="step_no">2</span>
									<span class="step_descr">
							Advanced Options<br />
							<small>All Advanced Options</small>
						</span>
								</a>
							</li>
							<li>
								<a href="#step-3">
									<span class="step_no">3</span>
									<span class="step_descr">
							Additional Options<br />
							<small>All Additional Options</small>
						</span>
								</a>
							</li>
						</ul>
						<div id="step-1">
					<div class="form-group">
						<div class="col-md-4" id="r_category-group">
							<label for="category">Category</label>
							<select class="form-control" name="Models[category]">
							<?php foreach(Category::find()->all() as $category) :?>
								<option value="<?php echo $category->id;?>"><?php echo $category->categoryname;?></option>
							<?php endforeach;?>
							</select>
						</div>
						<div class="col-md-4" id="r_manufacturer-group">
							<label for="manufacturer">Manufacturer</label>
							<select class="form-control" name="Models[manufacturer]" id="model_man">
							<?php foreach(Manufacturer::find()->all() as $manufacturer) :?>
								<option value="<?php echo $manufacturer->id;?>"><?php echo $manufacturer->name;?></option>
							<?php endforeach;?>
							</select>
						</div>	
						<div class="col-md-4" id="r_aei-group">
							<label for="aei">AEI#</label>
							<input type="text" class="form-control" name="Models[aei]" placeholder="AEI# (Required)" id="autocomplete-aei" required>
						</div>						
					</div>				
					<div class="form-group">
						<div class="col-md-12" id="r_description-group">
							<label for="description">Description</label>
							<textarea class="form-control " name="Models[descrip]" style="min-height:60px;resize:none;" id="model_descrip" placeholder="Description (Required)" required></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4" id="r_defaultpurchaseprice-group">
							<label for="defaultpurchaseprice">Default Purchase Price</label>
							<input type="text" class="form-control" name="Models[purchasepricing]" placeholder="Default Purchase Price (Required)">
						</div>
						<div class="col-md-4" id="r_defaultrepairprice-group">
							<label for="defaultrepairprice">Default Repair Price</label>
							<input type="text" class="form-control" name="Models[repairpricing]" placeholder="Default Repair Price (Required)">
						</div>						
						<div class="col-md-4" id="r_serialized-group">
							<label for="serialized">Serialized</label>
							<select class="form-control" name="Models[serialized]">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>						
						</div>						
					</div>					
						</div>
						<div id="step-2">
							<h2 class="StepTitle">Advanced Options</h2>
							<div class="form-group">
								<div class="col-md-6" id="r_fru-group">
									<label for="frunumber">FRU Number</label>
									<input type="text" class="form-control" name="Models[frupartnum]" placeholder="FRU#">
								</div>			
								<div class="col-md-6" id="r_man-group">
									<label for="manpartnumber">Man Part Number</label>
									<input type="text" class="form-control" name="Models[manpartnum]" placeholder="Man part number">
								</div>	
							</div>	
							<div class="form-group">
								<div class="col-md-6" id="vendor-group">
									<label for="pvendor">Prefered Vendor</label>
									<select class="form-control" id="selectVendor" name="Models[prefered_vendor]">
										<option selected="selected" value="">Select A Vendor</option>
										<?php $vendors = Vendor::find()->all();?>
								  		<?php foreach($vendors as $vendor) :?>	
											<option value="<?php echo $vendor->id;?>"><?php echo $vendor->vendorname;?></option>											
								  		<?php endforeach;?>
									</select>
								</div>
								<div class="col-md-6" id="r_pvc-group">
									<label for="pvc">Preferred Vendor Cost</label></br>
									<input type="text" class="form-control" name="pvendorcost" placeholder="0.00">
								</div>															
							</div>								
							<div class="form-group">
								<div class="col-md-4" id="r_svendor-group">
									<label for="svendor">Secondary Vendor</label>
									<select class="form-control" id="selectVendor" name="Models[secondary_vendor]">
										<option selected="selected" value="">Select A Vendor</option>
										<?php $vendors = Vendor::find()->all();?>
								  		<?php foreach($vendors as $vendor) :?>	
											<option value="<?php echo $vendor->id;?>"><?php echo $vendor->vendorname;?></option>											
								  		<?php endforeach;?>
									</select>
								</div>
								<div class="col-md-4" id="r_fru-group">
									<label for="">Secondary Vendor Cost</label>
									<input type="text" class="form-control" name="secondaryvendorcost" placeholder="0.00">
								</div>
								<div class="col-md-4" id="r_fru-group">
									<label for="">Last Cost</label>
									<input type="text" class="form-control" name="lastcost" placeholder="0.00">
								</div>																																
							</div>						
						</div>
						<div id="step-3">
							<h2 class="StepTitle">Additional Options</h2>
							<div class="form-group">
								<div class="col-md-6" id="r_fru-group">
									<label for="">Default quantity for reordering?</label>
									<input type="number" class="form-control" name="Models[reorderqty]">
								</div>	
								<div class="col-md-6" id="r_fru-group">
									<label for="">How many will fit on a pallet?</label>
									<input type="number" class="form-control" name="Models[palletqtylimit]">
								</div>															
							</div>	
							<div class="form-group">
								<div id="r_fru-group">
									<label for="">Remove these characters from the manufacturers serial number barcode:</label>
									<input type="text" class="form-control" name="Models[stripcharacters]">
								</div>																						
							</div>	
							<div class="form-group">
								<div id="r_fru-group">
									<label for="">Check the manufacturers serial number barcode for these characters:</label>
									<input type="text" class="form-control" name="Models[checkit]">
								</div>		
							</div>
							<div class="form-group">
								<div id="r_fru-group">
									<label for="">How many characters should the serial number be?</label>
									<input type="number" name="Models[charactercount]" class="form-control">
								</div>		
							</div>	
							<div class="form-group">
								<div id="r_fru-group">
									<label for="">Does this item qualify for photo conversion for serial number entry?</label>
									<input type="radio" name="photo_conversion" value="1"> Yes <input type="radio" name="photo_conversion" value="0"> No
								</div>		
							</div>
							<div class="form-group">
								<div id="r_fru-group">
									<label for="">Are pre-owned/used items of this model sent through the cleaning department before the service labs?</label>
									<input type="radio" name="preowneduseditems" value="1"> Yes <input type="radio" name="preowneduseditems" value="0"> No
								</div>		
							</div>																									
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Save</button>
			</div>
			<input type="hidden" id="entryRow" />
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>