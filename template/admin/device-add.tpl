{config_load file="devices.ini" section="add"}
<div class="modal fade" id="modal-adddev" tabindex="-1" role="dialog" aria-labelledby="adddev-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="devadd-t">{#AddDevice#}</h4>
      </div>
      <div class="modal-body" id="mb-newdev">
          <form role="form" id="form-new-dev">
              <div class="form-group">
                  <label>{#DeviceID#}</label>
                  <input type="text" id="in-dev-id" name="devid" class="form-control">
                  <p class="help-block">{#DevIDDesc#}</p>
              </div>
              <div class="form-group">
                  <label>{#DeviceName#}</label>
                  <input type="text" id="in-dev-name" name="devname" class="form-control">
                  <p class="help-block">{#DevNameDesc#}</p>
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
        <button type="button" class="btn btn-primary" id="newdevsave">{#Save#}</button>
      </div>
    </div>
  </div>
</div>