// boyalikelimeler.com — Front JS
// Vanilla JS | ES6+

/**
 * CSRF token for AJAX requests
 */
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

/**
 * Fetch API wrapper with CSRF support
 */
async function httpPost(url, data = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept':        'application/json',
            'X-CSRF-TOKEN':  csrfToken,
        },
        body: JSON.stringify(data),
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
}
