/**
 * Web Push Notification Handler
 */

class WebPush {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
    }

    async init() {
        if (!this.isSupported) return;

        this.registration = await navigator.serviceWorker.ready;
        
        // Check if already subscribed
        const subscription = await this.registration.pushManager.getSubscription();
        if (subscription) {
            await this.updateSubscriptionOnServer(subscription);
        }
    }

    async subscribe() {
        if (!this.isSupported) return;

        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return null;

            const subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(window.Laravel.vapidPublicKey)
            });

            await this.updateSubscriptionOnServer(subscription);
            return subscription;
        } catch (error) {
            console.error('Failed to subscribe to push notifications:', error);
            return null;
        }
    }

    async unsubscribe() {
        const subscription = await this.registration.pushManager.getSubscription();
        if (subscription) {
            await subscription.unsubscribe();
            await this.deleteSubscriptionOnServer(subscription);
        }
    }

    async updateSubscriptionOnServer(subscription) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        return fetch('/push-subscriptions', {
            method: 'POST',
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: {
                    auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                    p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null
                },
                content_encoding: contentEncoding
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }

    async deleteSubscriptionOnServer(subscription) {
        return fetch('/push-subscriptions/delete', {
            method: 'POST',
            body: JSON.stringify({
                endpoint: subscription.endpoint
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
}

window.WebPush = new WebPush();
document.addEventListener('DOMContentLoaded', () => window.WebPush.init());
