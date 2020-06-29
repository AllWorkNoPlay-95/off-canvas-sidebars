/* eslint-disable no-extra-semi */
;/**
 * Off-Canvas Sidebars - Setup Validate
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Off_Canvas_Sidebars
 * @since   0.5.6
 * @version 0.5.6
 * @global  ocsNavControl
 * @preserve
 */
/* eslint-enable no-extra-semi */

if ( 'undefined' === typeof ocsSetupValidate ) {
	var ocsSetupValidate = {
		messages: {
			error_website_before: '<code>website_before</code> hook is not correct.',
			error_website_after: '<code>website_after</code> hook is not correct.',
			hooks_correct: 'Theme hooks setup correct!'
		}
	};
}

( function( $ ) {

	var $body = $( 'body' );

	ocsSetupValidate.run = function() {

		var errors  = [],
			popup   = '',
			correct = false,
			color;

		if ( ! $body.children( '[canvas]' ).length ) {
			errors.push( ocsSetupValidate.messages.error_website_before );
		}
		if ( ! $body.children( '#ocs_validate_website_after' ).length ) {
			errors.push( ocsSetupValidate.messages.error_website_after );
		}

		// Do notice.
		if ( ! errors.length ) {
			correct = true;
			popup = ocsSetupValidate.messages.hooks_correct;
			ocsSetupValidate.log( ocsSetupValidate.messages.hooks_correct );
		} else {
			$.each( errors, function( i, m ) {
				i = i+1;
				popup += '<li>' + i + ': ' + m + '</li>';
				ocsSetupValidate.log( m );
			} );
			popup = '<ul style="list-style: none;">' + popup + '</ul>';
		}

		if ( correct ) {
			color = '#46b450';
		} else {
			color = '#dc3232';
		}

		var style = 'min-width: 250px; background: #fff; color: #000; border: 5px solid ' + color +  '; box-shadow: 0 0 15px; text-align: center; padding: 15px;';
		popup = '<div style="' + style + '">' + popup + '</div>';

		ocsSetupValidate.popup( popup );
	};

	ocsSetupValidate.popup = function( content ) {
		var style = 'background: rgba(0,0,0,0.5); position: fixed; display: flex; height: 100%; width: 100%; top: 0; left: 0; z-index: 9999999; display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; -webkit-box-pack: center; -webkit-justify-content: center; -ms-flex-pack: center; justify-content: center;';

		$('#ocs-setup-validate-result').remove();
		$body.append( '<div id="ocs-setup-validate-result" style="' + style + '">' + content + '</div>' );
	};

	ocsSetupValidate.log = function( message ) {
		console.log( 'Off-Canvas Sidebars Setup Validator: ' + message );
	};

	ocsSetupValidate.run();

} ( jQuery ) );
