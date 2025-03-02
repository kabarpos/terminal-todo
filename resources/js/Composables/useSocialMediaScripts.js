import { onMounted } from 'vue'

export function useSocialMediaScripts() {
    const loadScript = (src) => {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) {
                resolve()
                return
            }

            const script = document.createElement('script')
            script.src = src
            script.async = true
            script.onload = resolve
            script.onerror = reject
            document.body.appendChild(script)
        })
    }

    const initFacebook = () => {
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v18.0'
            })
        }
    }

    onMounted(() => {
        // Load Instagram
        loadScript('https://www.instagram.com/embed.js')
            .then(() => console.log('Instagram script loaded'))
            .catch(err => console.error('Failed to load Instagram script:', err))

        // Load Twitter
        loadScript('https://platform.twitter.com/widgets.js')
            .then(() => console.log('Twitter script loaded'))
            .catch(err => console.error('Failed to load Twitter script:', err))

        // Load Facebook
        loadScript('https://connect.facebook.net/id_ID/sdk.js')
            .then(() => {
                console.log('Facebook script loaded')
                initFacebook()
            })
            .catch(err => console.error('Failed to load Facebook script:', err))

        // Load TikTok
        loadScript('https://www.tiktok.com/embed.js')
            .then(() => console.log('TikTok script loaded'))
            .catch(err => console.error('Failed to load TikTok script:', err))
    })

    return {
        loadScript
    }
} 