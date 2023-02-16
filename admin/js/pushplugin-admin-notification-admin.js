(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	
	document.addEventListener('DOMContentLoaded', () => {
		let isPushEnabled = false;
	
		const pushButton = document.querySelector('#pushplugin-admin-notification-subscription-button');
		if (!pushButton) {
		  return;
		}
	
		pushButton.addEventListener('click', function () {
		  if (isPushEnabled) {
			push_unsubscribe();
		  } else {
			push_subscribe();
		  }
		});
	
		if (!('serviceWorker' in navigator)) {
		  console.warn('Service workers are not supported by this browser');
		  changePushButtonState('incompatible');
		  return;
		}
	
		if (!('PushManager' in window)) {
		  console.warn('Push notifications are not supported by this browser');
		  changePushButtonState('incompatible');
		  return;
		}
	
		if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
		  console.warn('Notifications are not supported by this browser');
		  changePushButtonState('incompatible');
		  return;
		}
	
		// Check the current Notification permission.
		// If its denied, the button should appears as such, until the user changes the permission manually
		if (Notification.permission === 'denied') {
		  console.warn('Notifications are denied by the user');
		  changePushButtonState('incompatible');
		  return;
		}
	
		navigator.serviceWorker.register(serviceWorkerFile).then(
		  () => {
			console.log('[SW] Service worker has been registered');
			push_updateSubscription();
		  },
		  e => {
			console.error('[SW] Service worker registration failed', e);
			changePushButtonState('incompatible');
		  }
		);
	
		function changePushButtonState(state) {
		  switch (state) {
			case 'enabled':
			  pushButton.disabled = false;
			  pushButton.textContent = 'Disable Push notifications';
			  isPushEnabled = true;
			  break;
			case 'disabled':
			  pushButton.disabled = false;
			  pushButton.innerHTML = '<div class="dashicons dashicons-megaphone" style="margin-right: 10px;" aria-hidden="true"></div> Enable Push notifications';
			  isPushEnabled = false;
			  break;
			case 'computing':
			  pushButton.disabled = true;
			  pushButton.textContent = 'Loading...';
			  break;
			case 'incompatible':
			  pushButton.disabled = true;
			  pushButton.textContent = 'Push notifications are not compatible with this browser';
			  break;
			default:
			  console.error('Unhandled push button state', state);
			  break;
		  }
		}
	
		function urlBase64ToUint8Array(base64String) {
		  const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
		  const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
	
		  const rawData = window.atob(base64);
		  const outputArray = new Uint8Array(rawData.length);
	
		  for (let i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		  }
		  return outputArray;
		}
	
		function checkNotificationPermission() {
		  return new Promise((resolve, reject) => {
			if (Notification.permission === 'denied') {
			  return reject(new Error('Push messages are blocked.'));
			}
	
			if (Notification.permission === 'granted') {
			  return resolve();
			}
	
			if (Notification.permission === 'default') {
			  return Notification.requestPermission().then(result => {
				if (result !== 'granted') {
				  reject(new Error('Bad permission result'));
				} else {
				  resolve();
				}
			  });
			}
	
			return reject(new Error('Unknown permission'));
		  });
		}
	
		function push_subscribe() {
		  changePushButtonState('computing');
			  
	
		  return checkNotificationPermission()
			.then(async () => {
				console.log(await navigator.serviceWorker.ready)
				return navigator.serviceWorker.ready
			})
			.then(serviceWorkerRegistration =>
				{
					return serviceWorkerRegistration.pushManager.subscribe({
					  userVisibleOnly: true,
					  applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
					})
				}
			)
			.then(subscription => {
			  // Subscription was successful
			  // create subscription on your server
			  return push_sendSubscriptionToServer(subscription, 'POST');
			})
			.then(subscription => subscription && changePushButtonState('enabled')) // update your UI
			.catch(e => {
			  if (Notification.permission === 'denied') {
				// The user denied the notification permission which
				// means we failed to subscribe and the user will need
				// to manually change the notification permission to
				// subscribe to push messages
				console.warn('Notifications are denied by the user.');
				changePushButtonState('incompatible');
			  } else {
				// A problem occurred with the subscription; common reasons
				// include network errors or the user skipped the permission
				console.error('Impossible to subscribe to push notifications', e);
				changePushButtonState('disabled');
			  }
			});
		}
	
		function push_updateSubscription() {
		  navigator.serviceWorker.ready
			.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
			.then(subscription => {
			  changePushButtonState('disabled');
	
			  if (!subscription) {
				// We aren't subscribed to push, so set UI to allow the user to enable push
				return;
			  }
	
			  // Keep your server in sync with the latest endpoint
			  return push_sendSubscriptionToServer(subscription, 'PUT');
			})
			.then(subscription => subscription && changePushButtonState('enabled')) // Set your UI to show they have subscribed for push messages
			.catch(e => {
			  console.error('Error when updating the subscription', e);
			});
		}
	
		function push_unsubscribe() {
		  changePushButtonState('computing');
	
		  // To unsubscribe from push messaging, you need to get the subscription object
		  navigator.serviceWorker.ready
			.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
			.then(subscription => {
			  // Check that we have a subscription to unsubscribe
			  if (!subscription) {
				// No subscription object, so set the state
				// to allow the user to subscribe to push
				changePushButtonState('disabled');
				return;
			  }
	
			  // We have a subscription, unsubscribe
			  // Remove push subscription from server
			  return push_sendSubscriptionToServer(subscription, 'DELETE');
			})
			.then(subscription => subscription.unsubscribe())
			.then(() => changePushButtonState('disabled'))
			.catch(e => {
			  // We failed to unsubscribe, this can lead to
			  // an unusual state, so  it may be best to remove
			  // the users data from your data store and
			  // inform the user that you have done so
			  console.error('Error when unsubscribing the user', e);
			  changePushButtonState('disabled');
			});
		}
	
		function push_sendSubscriptionToServer(subscription, method) {
		  console.log(subscription, method)
		  const key = subscription.getKey('p256dh');
		  const token = subscription.getKey('auth');
		  const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
	
		  return fetch(saveTokenPath, {
			method,
			body: JSON.stringify({
			  endpoint: subscription.endpoint,
			  publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
			  authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
			  contentEncoding,
			}),
		  }).then(() => subscription);
		}
	
		/**
		 * Revoke All tokens
		 * this part handles the button that calls the endpoint that revokes all tokens
		 */
	
		const revokeTokensButton = document.querySelector('#pushplugin-admin-notification-revoke-tokens-button');
		if (!revokeTokensButton) {
		  return;
		}

		revokeTokensButton.addEventListener('click', () => {
			fetch(revokeTokenPath, {
				method: 'POST',
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Bad status code from server.');
				}
				revokeTokensButton.disabled = true;
				return response.json();
			})
		});

		/**
		 * END send_push_notification
		 */
	  });

})( jQuery );
