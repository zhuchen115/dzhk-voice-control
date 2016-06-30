{config_load file="admin.ini" section="user-remove"}
<div class="modal fade" id="modal-uremove" tabindex="-1" role="dialog" aria-labelledby="uremove-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{#DeleteUser#}</h4>
      </div>
      <div class="modal-body" id="mb-uremove">
          <div class="alert alert-warning" role="alert"><strong>{#Warning#}</strong>{#URemoveDesc#}</div>
          <form role="form">
              <div class="form-group">
                  <label>UID</label>
                  <p class ="form-control-static" id="uremove-uid"></p>
              </div>
              <div class="form-group">
                  <label>{#UserName#}</label>
                  <p class ="form-control-static" id="uremove-username"></p>
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
        <button type="button" class="btn btn-danger" id="uremove-confirm">{#Confirm#}</button>
      </div>
    </div>
  </div>
</div>