{config_load file="devices.ini" section="tranfer"}
<div class="modal fade" id="modal-tfdev" tabindex="-1" role="dialog" aria-labelledby="adddev-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="tfdev-t">{#TransferDevice#}</h4>
            </div>
            <div class="modal-body" id="mb-tfdev">
                <h4>{#TransferStatus#}</h4>
                <p id="transfer-status"></p>
                <hr>
                <h4>{#TransferAction#}</h4>
                <button type="button" id="tf-action" data-devid="" data-action="transfer" class="btn btn-warning"></button>
                <p class="help-block" id="tfaction-help"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
            </div>
        </div>
    </div>
</div>