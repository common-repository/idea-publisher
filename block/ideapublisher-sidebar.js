(function (wp) { // look into https://github.com/WordPress/gutenberg-examples/blob/8e20a009dc8b70ba87bb200804d293897d375303/blocks-non-jsx/plugin-sidebar/plugin-sidebar.js to make it
  var registerPlugin = wp.plugins.registerPlugin;
  var PluginPrePublishPanel = wp.editPost.PluginPrePublishPanel;
  var el = wp.element.createElement;
  var TextareaControl = wp.components.TextareaControl;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;

  var MessageField = function ( props ) {
		var metaFieldValue = useSelect(function (select) {
      return select('core/editor').getEditedPostAttribute(
        'meta'
      )['ideapublisher_sidebar_message'];
    }, []);

    var editPost = useDispatch( 'core/editor' ).editPost;

		return el(TextareaControl, {
      label: 'Message to publish',
      help: 'What you want to share alongside the post link and title',
      value: metaFieldValue,
      onChange: function (content) {
        editPost({
          meta: { ideapublisher_sidebar_message: content },
        });
      },
    });
	};

  registerPlugin('ideapublisher-prepublish-sidebar', {
    render: function () {
      return el(
        PluginPrePublishPanel,
        {
          name: 'ideapublisher-prepublish-sidebar',
          icon: 'share-alt',
          title: 'Idea Publisher',
          initialOpen: true,
        },
        el(
          'div',
          { className: 'ideapublisher-prepublish-sidebar-content' },
          el( MessageField )
        )
      );
    },
  });
})(window.wp);