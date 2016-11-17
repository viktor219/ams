<!-- Modal -->
<div class="modal fade" id="transferInv" tabindex="-1" role="dialog" aria-labelledby="addLocationLabel" style="z-index:10000">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Merge Transfer: <span id="transfer_modal_name"></span></h4>
            </div>
            <div class="modal-body">
                <div id="search_item_loaded" class="clearfix"></div>
                <div id="model-item-details" style="display:none; margin-top: 10px">
                    <table width="100%" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Part Numbers</th>
                                <th>Category</th>
                                <th>Model</th>
                                <th>Inventory</th>
                                <th>Assembly</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Merge</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->