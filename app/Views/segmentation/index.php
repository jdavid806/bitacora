<div id="page-content" class="page-wrapper clearfix full-width-button">
  <div class="card segmentation-page">
    <div class="page-title clearfix">
      <h1><?php echo app_lang('segmentation_alerta_global'); ?></h1>
    </div>
    <div class="card-body">
      <form action="<?php echo site_url('segmentation/create'); ?>" method="post">
        <div class="form-group">
          <label for="nombre_alerta"><?php echo app_lang('nombre_alerta_global'); ?></label>
          <input type="text" class="form-control" id="nombre_alerta" name="nombre_alerta" required>
        </div>
        <div class="form-group">
          <label for="tipo_alerta"><?php echo app_lang('tipo_alerta_global'); ?></label>
          <select class="form-control" id="tipo_alerta" name="tipo_alerta" required>
            <option value=""><?php echo app_lang('selecciona_tipo_alerta_global'); ?></option>
            <option value="1"><?php echo app_lang('tipo_alerta_global_1'); ?></option>
            <option value="2"><?php echo app_lang('tipo_alerta_global_2'); ?></option>
            <!-- Agrega más opciones según sea necesario -->
          </select>
        </div>
        <div class="form-group">
          <label for="hora"><?php echo app_lang('hora_alerta_global'); ?></label>
          <input type="time" class="form-control" id="hora" name="hora" required>
        </div>
        <div class="form-group">
          <label for="fecha"><?php echo app_lang('fecha_alerta_global'); ?></label>
          <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo app_lang('guardar_alerta_global'); ?></button>
      </form>
    </div>
  </div>
</div>