class ApiHelper {
    constructor() {
        this.baseUrl = '/api';
        this.token = localStorage.getItem('api_token');
        this._csrfReady = false;
    }

    setToken(token) {
        this.token = token;
        if (token) {
            localStorage.setItem('api_token', token);
        } else {
            localStorage.removeItem('api_token');
        }
    }

    getHeaders() {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        const xsrf = this.getCookie('XSRF-TOKEN');
        if (xsrf) {
            headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf);
        }

        return headers;
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    async ensureCsrfCookie() {
        if (this._csrfReady) return;
        try {
            await fetch('/sanctum/csrf-cookie', {
                method: 'GET',
                credentials: 'include',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
        } catch (e) {
        } finally {
            this._csrfReady = true;
        }
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        await this.ensureCsrfCookie();

        const config = {
            headers: this.getHeaders(),
            credentials: 'include',
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                const message = data.message || (typeof data === 'string' ? data : 'Erro na requisição');
                throw new Error(message);
            }

            return data;
        } catch (error) {
            console.error('Erro na API:', error);
            throw error;
        }
    }

    async login(email, password) {
        const response = await this.request('/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (response.access_token) {
            this.setToken(response.access_token);
        } else if (response.token) {
            this.setToken(response.token);
        }

        return response;
    }

    async register(data) {
        return await this.request('/register', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async logout() {
        try {
            await this.request('/logout', { method: 'POST' });
        } catch (e) {
        } finally {
            this.setToken(null);
        }
    }

    async getProfile() {
        return await this.request('/me');
    }

    async updateProfile(data) {
        return await this.request('/profile', {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = queryString ? `/products?${queryString}` : '/products';
        return await this.request(endpoint);
    }

    async getProduct(id) {
        return await this.request(`/products/${id}`);
    }

    async createProduct(data) {
        return await this.request('/products', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateProduct(id, data) {
        return await this.request(`/products/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteProduct(id) {
        return await this.request(`/products/${id}`, {
            method: 'DELETE'
        });
    }

    async getProductsByCategory(categoryId) {
        return await this.request(`/products/category/${categoryId}`);
    }

    async getProductsWithImage() {
        return await this.request('/products/with-image');
    }

    async getProductsWithoutImage() {
        return await this.request('/products/without-image');
    }

    async getCategories() {
        return await this.request('/categories');
    }

    async getCategory(id) {
        return await this.request(`/categories/${id}`);
    }

    async createCategory(data) {
        return await this.request('/categories', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateCategory(id, data) {
        return await this.request(`/categories/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteCategory(id) {
        return await this.request(`/categories/${id}`, {
            method: 'DELETE'
        });
    }

    async importAllProducts() {
        return await this.request('/import/all', { method: 'POST' });
    }

    async importProduct(id) {
        return await this.request(`/import/${id}`, { method: 'POST' });
    }
}

// Instância global
window.api = new ApiHelper();

// Helper para mostrar notificações
window.showNotification = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
};

// Helper para confirmar ações
window.confirmAction = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};
