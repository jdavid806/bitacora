<script type="text/javascript">
var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
  
loadEditor = () => {
    const options = {};
    this.editor = unlayer.createEditor({
      ...options,
      id: 'EmailEditor',
      displayMode: 'email',
    });

    if($("input[name=data_design]").val() != ''){
      this.editor.loadDesign(JSON.parse(JSON.parse($("input[name=data_design]").val())));
    }

  registerCallback = (type, callback) => {
    this.editor.registerCallback(type, callback);
  };

  addEventListener = (type, callback) => {
    this.editor.addEventListener(type, callback);
  };

  loadDesign = (design) => {
    this.editor.loadDesign(design);
  };

  saveDesign = (callback) => {
    this.editor.saveDesign((design) => {
      console.log(design);
      $("input[name=data_design]").val(JSON.stringify(design, false));
    });
  };

  exportHtml = (callback) => {
    this.editor.exportHtml((data) => {
      const { design, html } = data;
      console.log(html);
      $("input[name=data_html]").val(html);
    });
  };

  setMergeTags = (mergeTags) => {
    this.editor.setMergeTags(mergeTags);
  };
}

(function($) {
    "use strict";

    loadScript(loadEditor);

})(jQuery);

function save_template(){
  saveDesign();
  exportHtml();

  $(this).attr('disabled', true);

  setTimeout(function(){
      console.log($("input[name=data_design]").val());

      $('#email-template-form').submit();
    }, 1000);
}
</script>
