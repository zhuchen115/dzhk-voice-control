{config_load file="admin.ini" section="user-chemail"}
<div class="modal fade" id="modal-uemail" tabindex="-1" role="dialog" aria-labelledby="chemail-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{#ChangeEmail#}</h4>
      </div>
      <div class="modal-body" id="mb-chemail">
          <form role="form">
              <div class="form-group">
                  <label>UID</label>
                  <p class ="form-control-static" id="chemail-uid"></p>
              </div>
              <div class="form-group">
                  <label>{#UserName#}</label>
                  <p class ="form-control-static" id="chemail-username"></p>
              </div>
              <div class="form-group">
                  <label>{#ChangeEmail#}</label>
                  <input type="text" id="chemail-email" name="email" class="form-control">
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
        <button type="button" class="btn btn-primary" id="chemail-save">{#Save#}</button>
      </div>
    </div>
  </div>
</div>