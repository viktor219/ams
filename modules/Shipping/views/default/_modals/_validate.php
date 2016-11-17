<!-- Modal -->
<div class="modal fade" id="validateModal" tabindex="-1" role="dialog" aria-labelledby="newModelLabel" style="z-index:10000">
    <div class="modal-dialog" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Weights & Dimensions Error</h4>
            </div>
            <div class="modal-body">
                Please add Weights & Dimensions for following <?php echo (!$ispallet)?"Box":"Pallet"; ?> Numbers: 
                <span class="error"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>