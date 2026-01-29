/**
 * Authentication API Client for Clothes Shop
 * This file contains all the authentication-related API calls
 */

const API_BASE_URL = 'http://localhost:8080/api';

/**
 * Storage keys
 */
const TOKEN_KEY = 'accessToken';
const REFRESH_TOKEN_KEY = 'refreshToken';
const USER_KEY = 'user';

/**
 * Register a new user
 */
async function register(userData) {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: userData.email,
                password: userData.password,
                firstName: userData.firstName,
                lastName: userData.lastName,
                phone: userData.phone
            })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Registration failed');
        }

        const data = await response.json();

        // Store tokens and user info
        localStorage.setItem(TOKEN_KEY, data.accessToken);
        localStorage.setItem(REFRESH_TOKEN_KEY, data.refreshToken);
        localStorage.setItem(USER_KEY, JSON.stringify({
            userId: data.userId,
            email: data.email,
            firstName: data.firstName,
            lastName: data.lastName,
            role: data.role
        }));

        return data;
    } catch (error) {
        console.error('Registration error:', error);
        throw error;
    }
}

/**
 * Login user
 */
async function login(credentials) {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: credentials.email,
                password: credentials.password
            })
        });

        if (!response.ok) {
            throw new Error('Invalid email or password');
        }

        const data = await response.json();

        // Store tokens and user info
        localStorage.setItem(TOKEN_KEY, data.accessToken);
        localStorage.setItem(REFRESH_TOKEN_KEY, data.refreshToken);
        localStorage.setItem(USER_KEY, JSON.stringify({
            userId: data.userId,
            email: data.email,
            firstName: data.firstName,
            lastName: data.lastName,
            role: data.role
        }));

        return data;
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
}

/**
 * Logout user
 */
function logout() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(REFRESH_TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    window.location.href = '/login.html';
}

/**
 * Get access token
 */
function getAccessToken() {
    return localStorage.getItem(TOKEN_KEY);
}

/**
 * Get refresh token
 */
function getRefreshToken() {
    return localStorage.getItem(REFRESH_TOKEN_KEY);
}

/**
 * Get current user
 */
function getCurrentUser() {
    const userStr = localStorage.getItem(USER_KEY);
    return userStr ? JSON.parse(userStr) : null;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return !!getAccessToken();
}

/**
 * Refresh access token
 */
async function refreshAccessToken() {
    try {
        const refreshToken = getRefreshToken();
        if (!refreshToken) {
            throw new Error('No refresh token available');
        }

        const response = await fetch(`${API_BASE_URL}/auth/refresh`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ refreshToken })
        });

        if (!response.ok) {
            logout();
            throw new Error('Token refresh failed');
        }

        const data = await response.json();

        // Update tokens
        localStorage.setItem(TOKEN_KEY, data.accessToken);
        localStorage.setItem(REFRESH_TOKEN_KEY, data.refreshToken);

        return data.accessToken;
    } catch (error) {
        console.error('Token refresh error:', error);
        logout();
        throw error;
    }
}

/**
 * Make authenticated API call
 */
async function authenticatedFetch(url, options = {}) {
    const token = getAccessToken();

    if (!token) {
        throw new Error('No authentication token available');
    }

    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        ...options.headers
    };

    try {
        let response = await fetch(url, {
            ...options,
            headers
        });

        // If token expired, try to refresh
        if (response.status === 401) {
            const newToken = await refreshAccessToken();
            headers.Authorization = `Bearer ${newToken}`;

            response = await fetch(url, {
                ...options,
                headers
            });
        }

        return response;
    } catch (error) {
        console.error('Authenticated fetch error:', error);
        throw error;
    }
}

/**
 * Get current user profile
 */
async function getUserProfile() {
    try {
        const response = await authenticatedFetch(`${API_BASE_URL}/auth/me`);

        if (!response.ok) {
            throw new Error('Failed to get user profile');
        }

        return await response.json();
    } catch (error) {
        console.error('Get profile error:', error);
        throw error;
    }
}

/**
 * Get all products (public)
 */
async function getProducts() {
    try {
        const response = await fetch(`${API_BASE_URL}/products`);
        return await response.json();
    } catch (error) {
        console.error('Get products error:', error);
        throw error;
    }
}

/**
 * Add item to cart (authenticated)
 */
async function addToCart(productId, quantity = 1) {
    try {
        const response = await authenticatedFetch(`${API_BASE_URL}/cart/add`, {
            method: 'POST',
            body: JSON.stringify({ productId, quantity })
        });

        if (!response.ok) {
            throw new Error('Failed to add to cart');
        }

        return await response.json();
    } catch (error) {
        console.error('Add to cart error:', error);
        throw error;
    }
}

/**
 * Get user's cart (authenticated)
 */
async function getCart() {
    try {
        const user = getCurrentUser();
        if (!user) {
            throw new Error('User not authenticated');
        }

        const response = await authenticatedFetch(`${API_BASE_URL}/cart/${user.userId}`);

        if (!response.ok) {
            throw new Error('Failed to get cart');
        }

        return await response.json();
    } catch (error) {
        console.error('Get cart error:', error);
        throw error;
    }
}

/**
 * Create checkout (authenticated)
 */
async function checkout(checkoutData) {
    try {
        const response = await authenticatedFetch(`${API_BASE_URL}/checkout/create`, {
            method: 'POST',
            body: JSON.stringify(checkoutData)
        });

        if (!response.ok) {
            throw new Error('Checkout failed');
        }

        return await response.json();
    } catch (error) {
        console.error('Checkout error:', error);
        throw error;
    }
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        register,
        login,
        logout,
        getAccessToken,
        getRefreshToken,
        getCurrentUser,
        isAuthenticated,
        refreshAccessToken,
        authenticatedFetch,
        getUserProfile,
        getProducts,
        addToCart,
        getCart,
        checkout
    };
}
