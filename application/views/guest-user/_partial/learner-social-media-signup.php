<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="group group--social">
	<a class="facebook-login" href="javascript:void(0)" onclick="dofacebookInLoginForBuyerpopup()">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 60.734 60.733" style="enable-background:new 0 0 60.734 60.733;" xml:space="preserve">
			<g>
				<path d="M57.378,0.001H3.352C1.502,0.001,0,1.5,0,3.353v54.026c0,1.853,1.502,3.354,3.352,3.354h29.086V37.214h-7.914v-9.167h7.914   v-6.76c0-7.843,4.789-12.116,11.787-12.116c3.355,0,6.232,0.251,7.071,0.36v8.198l-4.854,0.002c-3.805,0-4.539,1.809-4.539,4.462   v5.851h9.078l-1.187,9.166h-7.892v23.52h15.475c1.852,0,3.355-1.503,3.355-3.351V3.351C60.731,1.5,59.23,0.001,57.378,0.001z" />
			</g>
		</svg>
		<span><?php echo Label::getLabel("LBL_Sign_in_with_Facebook") ?></span>
	</a>
	<a class="google-login" href="<?php echo CommonHelper::generateUrl('GuestUser', 'socialMediaLogin', array('google', $userType)); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
			<path style="fill:#FBBB00;" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256  c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456  C103.821,274.792,107.225,292.797,113.47,309.408z" />
			<path style="fill:#518EF8;" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451  c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535  c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z" />
			<path style="fill:#28B446;" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512  c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771  c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z" />
			<path style="fill:#F14336;" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012  c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0  C318.115,0,375.068,22.126,419.404,58.936z" />
			<g>
		</svg>
		<span><?php echo Label::getLabel("LBL_Sign_in_with_Google") ?></span>
	</a>
</div>

<span class="-gap"></span>
<script>
	/*Facebook Login API JS SDK*/

	function dofacebookInLoginForBuyerpopup() {
		FB.login(function(response) {
			if (response.status === 'connected' && response.authResponse) {
				getUserData(response.authResponse.accessToken);
			}
		}, {
			scope: 'email,public_profile',
			return_scopes: true
		});
	}

	function getUserData(accessToken) {
		FB.api('/me?fields=id,name,email, first_name, last_name', function(response) {
			console.log(response);
			if (!response.id || typeof response == "undefined" || response.error) {
				return;
			} else {

				response['type'] = <?php echo $userType; ?>;
				response['accessToken'] = accessToken;
				fcom.updateWithAjax(fcom.makeUrl('GuestUser', 'loginFacebook'), response, function(t) {
					location.href = t.url;
				});
			}

		}, {
			scope: 'public_profile,email'
		});
	}

	window.fbAsyncInit = function() {
		//SDK loaded, initialize it
		FB.init({
			appId: '<?php echo FatApp::getConfig('CONF_FACEBOOK_APP_ID', FatUtility::VAR_STRING, '') ?>',
			xfbml: true,
			version: 'v2.2'
		});
	};

	//load the JavaScript SDK
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "https://connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	/*Facebook Login API JS SDK*/
</script>