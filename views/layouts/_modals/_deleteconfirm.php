<!-- Modal -->
<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="deleteConfirmLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Delete  <?=$page;?> ?</h4>
            </div>
            <form class="form-group form-group-sm" id="o-add-location-form" >	
                <div class="modal-body">
                    Are you sure you want to Delete ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
                    <a href="" id="yes-delete-order" type="button" class="btn btn-danger">Yes</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End -->