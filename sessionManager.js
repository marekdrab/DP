function setToSessionStorage(data) {
    try {
        sessionStorage.setItem('access_token', data.access_token);
        sessionStorage.setItem('expires_at', data.expires_at);
        sessionStorage.setItem('refresh_token', data.refresh_token);
        sessionStorage.setItem('refresh_token_expires_at', data.refresh_token_expires_at);
    } catch (error) {
        console.error('Error setting session storage item:', error);
    }
}
