<div class="modal fade" id="modal-stock-movement" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Stock Movement</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Product:</strong> <span id="product-name"></span><br>
                        <strong>Current Stock:</strong> <span id="current-stock"></span>
                    </div>

                    <div class="form-group row">
                        <label for="tipe" class="col-lg-3 control-label">Movement Type</label>
                        <div class="col-lg-8">
                            <select name="tipe" id="tipe" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="masuk">Stock In (+)</option>
                                <option value="keluar">Stock Out (-)</option>
                                <option value="rusak">Damaged (-)</option>
                                <option value="penyesuaian">Adjustment</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jumlah" class="col-lg-3 control-label">Quantity</label>
                        <div class="col-lg-8">
                            <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lokasi" class="col-lg-3 control-label">Location</label>
                        <div class="col-lg-8">
                            <select name="lokasi" id="lokasi" class="form-control">
                                <option value="">Select Location</option>
                                <option value="Cuisine">Cuisine (Kitchen)</option>
                                <option value="Maison">Maison (House)</option>
                                <option value="Cafet">Cafet (Cafeteria)</option>
                                <option value="Avarie">Avarie (Damaged)</option>
                                <option value="resto">Resto</option>
                                <option value="Bar">Bar</option>
                                <option value="Stock">Stock/Warehouse</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="keterangan" class="col-lg-3 control-label">Notes</label>
                        <div class="col-lg-8">
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control"></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-success">
                        <i class="fa fa-save"></i> Save Movement
                    </button>
                    <button type="button" class="btn btn-sm btn-flat btn-danger" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>