{config_load file="admin.ini" section="user-cname"}
<div class="modal fade" id="modal-ucname" tabindex="-1" role="dialog" aria-labelledby="cname-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#ChangeName#}</h4>
            </div>
            <div class="modal-body" id="mb-cname">
                <form role="form">
                    <div class="form-group">
                        <label>UID</label>
                        <p class ="form-control-static" id="chname-uid"></p>
                    </div>
                    <div class="form-group">
                        <label>{#UserName#}</label>
                        <p class ="form-control-static" id="chname-username"></p>
                    </div>
                    <div class="form-group">
                        <label>{#DispName#}</label>
                        <input type="text" id="input-dispname" class="form-control" value="{if isset($user.extra.name)}{$user.extra.name}{/if}" data-utype="name">
                        <p class="help-block" >{#DescDispName#}</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
                <button type="button" class="btn btn-primary" id="chname-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>