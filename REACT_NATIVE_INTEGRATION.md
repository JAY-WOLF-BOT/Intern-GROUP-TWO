# React Native API Client Implementation

Complete guide for integrating the Accra Housing Marketplace API with React Native.

---

## 1. Setup

### Install Dependencies

```bash
npm install axios react-native-async-storage
# or
yarn add axios react-native-async-storage
```

### Create API Client Service

**File: `services/apiClient.ts`**

```typescript
import axios, { AxiosInstance } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

class ApiClient {
  private client: AxiosInstance;
  private baseURL: string;

  constructor() {
    this.baseURL = process.env.REACT_APP_API_URL || 'https://api.accrahousing.com/api/v1';
    
    this.client = axios.create({
      baseURL: this.baseURL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Request interceptor - add auth token
    this.client.interceptors.request.use(
      async (config) => {
        const token = await AsyncStorage.getItem('authToken');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor - handle errors
    this.client.interceptors.response.use(
      (response) => response,
      async (error) => {
        if (error.response?.status === 401) {
          // Token expired
          await this.refreshToken();
        }
        return Promise.reject(error);
      }
    );
  }

  /**
   * Get stored auth token
   */
  async getToken(): Promise<string | null> {
    return await AsyncStorage.getItem('authToken');
  }

  /**
   * Set auth token
   */
  async setToken(token: string): Promise<void> {
    await AsyncStorage.setItem('authToken', token);
  }

  /**
   * Remove auth token
   */
  async removeToken(): Promise<void> {
    await AsyncStorage.removeItem('authToken');
  }

  /**
   * Refresh token
   */
  async refreshToken(): Promise<boolean> {
    try {
      const response = await this.client.post('/auth/refresh');
      const newToken = response.data.data.token;
      await this.setToken(newToken);
      return true;
    } catch (error) {
      // Refresh failed - user needs to login again
      await this.removeToken();
      return false;
    }
  }

  /**
   * Get raw axios client for custom requests
   */
  getClient(): AxiosInstance {
    return this.client;
  }
}

export const apiClient = new ApiClient();
```

---

## 2. Authentication Service

**File: `services/authService.ts`**

```typescript
import { apiClient } from './apiClient';

export interface User {
  id: number;
  name: string;
  email: string;
  phone_number: string;
  role: 'tenant' | 'landlord';
  profile_info?: {
    bio?: string;
    avatar?: string;
  };
  created_at: string;
}

export interface AuthResponse {
  user: User;
  token: string;
  token_type: 'Bearer';
}

class AuthService {
  /**
   * Register new user
   */
  async register(data: {
    name: string;
    email: string;
    phone_number: string;
    password: string;
    password_confirmation: string;
    role: 'tenant' | 'landlord';
  }): Promise<AuthResponse> {
    const response = await apiClient.getClient().post('/auth/register', data);
    
    if (response.data.data.token) {
      await apiClient.setToken(response.data.data.token);
    }
    
    return response.data.data;
  }

  /**
   * Login with email and password
   */
  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await apiClient.getClient().post('/auth/login', {
      email,
      password,
    });
    
    if (response.data.data.token) {
      await apiClient.setToken(response.data.data.token);
    }
    
    return response.data.data;
  }

  /**
   * Login with OTP
   */
  async loginWithOtp(phoneNumber: string, otpCode: string): Promise<AuthResponse> {
    const response = await apiClient.getClient().post('/auth/login-with-otp', {
      phone_number: phoneNumber,
      otp_code: otpCode,
    });
    
    if (response.data.data.token) {
      await apiClient.setToken(response.data.data.token);
    }
    
    return response.data.data;
  }

  /**
   * Get current user
   */
  async getCurrentUser(): Promise<User> {
    const response = await apiClient.getClient().get('/auth/me');
    return response.data.data;
  }

  /**
   * Logout
   */
  async logout(): Promise<void> {
    try {
      await apiClient.getClient().post('/auth/logout');
    } finally {
      await apiClient.removeToken();
    }
  }

  /**
   * Request OTP
   */
  async requestOtp(phoneNumber: string): Promise<void> {
    await apiClient.getClient().post('/otp/request', {
      phone_number: phoneNumber,
    });
  }

  /**
   * Verify OTP
   */
  async verifyOtp(phoneNumber: string, otpCode: string): Promise<void> {
    await apiClient.getClient().post('/otp/verify', {
      phone_number: phoneNumber,
      otp_code: otpCode,
    });
  }
}

export const authService = new AuthService();
```

---

## 3. Listing Service

**File: `services/listingService.ts`**

```typescript
import { apiClient } from './apiClient';

export interface Photo {
  id: number;
  url: string;
  is_primary: boolean;
  order: number;
}

export interface Landlord {
  id: number;
  name: string;
  phone_number: string;
  profile_info?: {
    bio?: string;
    avatar?: string;
  };
}

export interface Location {
  address: string;
  latitude: number;
  longitude: number;
}

export interface Listing {
  id: number;
  title: string;
  description: string;
  price: number;
  bedrooms: number;
  bathrooms: number;
  property_type: 'apartment' | 'house' | 'studio' | 'shared_room' | 'bungalow';
  neighborhood: string;
  location: Location;
  verification_status: 'pending' | 'verified' | 'rejected';
  is_available: boolean;
  view_count: number;
  photos: Photo[];
  landlord: Landlord;
  whatsapp_link: string;
  is_favorited: boolean;
  created_at: string;
  updated_at: string;
}

export interface ListingFilters {
  budget_min?: number;
  budget_max?: number;
  neighborhood?: string;
  bedrooms?: number;
  property_type?: string;
  sort_by?: 'price' | 'created_at' | 'bedrooms' | 'view_count';
  sort_order?: 'asc' | 'desc';
  page?: number;
}

class ListingService {
  /**
   * Search listings
   */
  async searchListings(filters?: ListingFilters): Promise<{
    listings: Listing[];
    meta: {
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
    };
  }> {
    const response = await apiClient.getClient().get('/listings', {
      params: filters,
    });
    
    return {
      listings: response.data.data,
      meta: response.data.meta,
    };
  }

  /**
   * Get single listing
   */
  async getListing(listingId: number): Promise<Listing> {
    const response = await apiClient.getClient().get(`/listings/${listingId}`);
    return response.data.data;
  }

  /**
   * Create listing (landlord)
   */
  async createListing(data: {
    title: string;
    description: string;
    price: number;
    bedrooms: number;
    bathrooms: number;
    property_type: string;
    neighborhood: string;
    location_address: string;
    location_lat: number;
    location_long: number;
  }): Promise<Listing> {
    const response = await apiClient.getClient().post('/listings', data);
    return response.data.data;
  }

  /**
   * Update listing
   */
  async updateListing(
    listingId: number,
    data: Partial<{
      title: string;
      description: string;
      price: number;
      bedrooms: number;
      bathrooms: number;
      is_available: boolean;
    }>
  ): Promise<Listing> {
    const response = await apiClient.getClient().put(`/listings/${listingId}`, data);
    return response.data.data;
  }

  /**
   * Delete listing
   */
  async deleteListing(listingId: number): Promise<void> {
    await apiClient.getClient().delete(`/listings/${listingId}`);
  }

  /**
   * Get my listings (landlord)
   */
  async getMyListings(page: number = 1): Promise<{
    listings: Listing[];
    meta: {
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
    };
  }> {
    const response = await apiClient.getClient().get('/listings/landlord/my-listings', {
      params: { page },
    });
    
    return {
      listings: response.data.data,
      meta: response.data.meta,
    };
  }
}

export const listingService = new ListingService();
```

---

## 4. Favorites Service

**File: `services/favoriteService.ts`**

```typescript
import { apiClient } from './apiClient';
import { Listing } from './listingService';

class FavoriteService {
  /**
   * Add to favorites
   */
  async addFavorite(listingId: number): Promise<{ favorite_id: number }> {
    const response = await apiClient.getClient().post('/favorites/add', {
      listing_id: listingId,
    });
    return { favorite_id: response.data.data.favorite_id };
  }

  /**
   * Remove from favorites
   */
  async removeFavorite(favoriteId: number): Promise<void> {
    await apiClient.getClient().delete(`/favorites/${favoriteId}`);
  }

  /**
   * Get my favorites
   */
  async getMyFavorites(page: number = 1): Promise<{
    listings: Listing[];
    meta: {
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
    };
  }> {
    const response = await apiClient.getClient().get('/favorites/my-favorites', {
      params: { page },
    });
    
    return {
      listings: response.data.data,
      meta: response.data.meta,
    };
  }

  /**
   * Check if listing is favorited
   */
  async isFavorited(listingId: number): Promise<boolean> {
    const response = await apiClient.getClient().get(`/favorites/is-favorited/${listingId}`);
    return response.data.is_favorited;
  }

  /**
   * Clear all favorites
   */
  async clearAllFavorites(): Promise<void> {
    await apiClient.getClient().post('/favorites/clear-all');
  }
}

export const favoriteService = new FavoriteService();
```

---

## 5. Payment Service

**File: `services/paymentService.ts`**

```typescript
import { apiClient } from './apiClient';

export interface Payment {
  id: number;
  payment_id: string;
  transaction_id: string;
  amount: number;
  payment_type: 'viewing_fee' | 'deposit_holding';
  payment_method: 'momo';
  payment_status: 'pending' | 'completed' | 'failed' | 'cancelled';
  momo_network?: 'MTN' | 'Vodafone' | 'AirtelTigo';
  description: string;
  listing?: {
    id: number;
    title: string;
    price: number;
  };
  paid_at?: string;
  created_at: string;
}

class PaymentService {
  /**
   * Initiate payment
   */
  async initiatePayment(data: {
    listing_id: number;
    payment_type: 'viewing_fee' | 'deposit_holding';
    payment_method: 'momo';
    momo_network: 'MTN' | 'Vodafone' | 'AirtelTigo';
    phone_number: string;
  }): Promise<{
    payment: Payment;
    instructions: {
      network: string;
      amount: string;
      ussd_code: string;
      description: string;
    };
  }> {
    const response = await apiClient.getClient().post('/payments/initiate', data);
    return response.data.data;
  }

  /**
   * Check payment status
   */
  async checkPaymentStatus(paymentId: string): Promise<Payment> {
    const response = await apiClient.getClient().get(`/payments/status/${paymentId}`);
    return response.data.data;
  }

  /**
   * Get payment history
   */
  async getPaymentHistory(filters?: {
    status?: string;
    type?: string;
    from_date?: string;
    to_date?: string;
    page?: number;
  }): Promise<{
    payments: Payment[];
    meta: {
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
      total_spent: string;
    };
  }> {
    const response = await apiClient.getClient().get('/payments/history', {
      params: filters,
    });
    
    return {
      payments: response.data.data,
      meta: response.data.meta,
    };
  }
}

export const paymentService = new PaymentService();
```

---

## 6. Usage Examples

### Example 1: Login & Get Listings

```typescript
import { authService } from './services/authService';
import { listingService } from './services/listingService';

async function login() {
  try {
    const auth = await authService.login('user@example.com', 'password123');
    console.log('Logged in as:', auth.user.name);
    
    // Fetch listings immediately after login
    const { listings } = await listingService.searchListings({
      page: 1,
      sort_by: 'created_at',
      sort_order: 'desc',
    });
    
    console.log('Found', listings.length, 'listings');
  } catch (error) {
    console.error('Login failed:', error);
  }
}
```

### Example 2: Search with Filters

```typescript
async function searchListings() {
  try {
    const { listings, meta } = await listingService.searchListings({
      budget_min: 500,
      budget_max: 2000,
      neighborhood: 'Osu',
      bedrooms: 2,
      property_type: 'apartment',
      sort_by: 'price',
      sort_order: 'asc',
      page: 1,
    });
    
    console.log(`Found ${meta.total} listings`);
    listings.forEach((listing) => {
      console.log(`${listing.title} - ${listing.price} GHS`);
    });
  } catch (error) {
    console.error('Search failed:', error);
  }
}
```

### Example 3: Manage Favorites

```typescript
async function manageFavorites() {
  try {
    // Add to favorites
    const { favorite_id } = await favoriteService.addFavorite(1);
    console.log('Added to favorites:', favorite_id);
    
    // Check if favorited
    const isFav = await favoriteService.isFavorited(1);
    console.log('Is favorited:', isFav);
    
    // Get all favorites
    const { listings } = await favoriteService.getMyFavorites();
    console.log('You have', listings.length, 'favorites');
    
    // Remove from favorites
    await favoriteService.removeFavorite(favorite_id);
    console.log('Removed from favorites');
  } catch (error) {
    console.error('Favorite operation failed:', error);
  }
}
```

### Example 4: Make Payment

```typescript
async function makePayment() {
  try {
    const { payment, instructions } = await paymentService.initiatePayment({
      listing_id: 1,
      payment_type: 'viewing_fee',
      payment_method: 'momo',
      momo_network: 'MTN',
      phone_number: '+233501234567',
    });
    
    console.log('Payment initiated:', payment.payment_id);
    console.log('USSD Code:', instructions.ussd_code);
    console.log('Amount:', instructions.amount);
    
    // Start polling for status
    const pollStatus = setInterval(async () => {
      const status = await paymentService.checkPaymentStatus(payment.payment_id);
      
      if (status.payment_status === 'completed') {
        console.log('Payment completed!');
        clearInterval(pollStatus);
      } else if (status.payment_status === 'failed') {
        console.log('Payment failed!');
        clearInterval(pollStatus);
      }
    }, 3000);
  } catch (error) {
    console.error('Payment failed:', error);
  }
}
```

### Example 5: Create Listing (Landlord)

```typescript
async function createListing() {
  try {
    const newListing = await listingService.createListing({
      title: 'Spacious 3-Bedroom House in East Legon',
      description: 'Beautiful house with large garden',
      price: 2500,
      bedrooms: 3,
      bathrooms: 2,
      property_type: 'house',
      neighborhood: 'East Legon',
      location_address: '123 Airport Road, East Legon',
      location_lat: 5.5950,
      location_long: -0.1748,
    });
    
    console.log('Listing created:', newListing.id);
    console.log('Status:', newListing.verification_status);
  } catch (error) {
    console.error('Failed to create listing:', error);
  }
}
```

---

## 7. React Context for Global State

**File: `contexts/AuthContext.tsx`**

```typescript
import React, { createContext, useState, useCallback } from 'react';
import { authService, User } from '../services/authService';

type AuthContextType = {
  user: User | null;
  isLoading: boolean;
  isLoggedIn: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  register: (data: any) => Promise<void>;
  getCurrentUser: () => Promise<void>;
};

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const login = useCallback(async (email: string, password: string) => {
    setIsLoading(true);
    try {
      const auth = await authService.login(email, password);
      setUser(auth.user);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const logout = useCallback(async () => {
    setIsLoading(true);
    try {
      await authService.logout();
      setUser(null);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const register = useCallback(async (data: any) => {
    setIsLoading(true);
    try {
      const auth = await authService.register(data);
      setUser(auth.user);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const getCurrentUser = useCallback(async () => {
    try {
      const currentUser = await authService.getCurrentUser();
      setUser(currentUser);
    } catch (error) {
      setUser(null);
    }
  }, []);

  return (
    <AuthContext.Provider
      value={{
        user,
        isLoading,
        isLoggedIn: !!user,
        login,
        logout,
        register,
        getCurrentUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = React.useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};
```

---

## 8. Environment Configuration

**File: `.env`**

```
REACT_APP_API_URL=https://api.accrahousing.com/api/v1
REACT_APP_DEBUG=false
```

**File: `.env.local` (development)**

```
REACT_APP_API_URL=http://localhost:8000/api/v1
REACT_APP_DEBUG=true
```

---

## 9. Error Handling

```typescript
export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

export const handleApiError = (error: any): ApiError => {
  if (error.response?.data) {
    return {
      message: error.response.data.message || 'An error occurred',
      errors: error.response.data.errors,
    };
  }
  
  if (error.message === 'Network Error') {
    return {
      message: 'Please check your internet connection',
    };
  }
  
  return {
    message: error.message || 'An unexpected error occurred',
  };
};

// Usage
try {
  await loginUser();
} catch (error) {
  const apiError = handleApiError(error);
  console.error(apiError.message);
  if (apiError.errors) {
    Object.entries(apiError.errors).forEach(([field, messages]) => {
      console.error(`${field}: ${messages.join(', ')}`);
    });
  }
}
```

---

**Created:** January 2024
**Updated:** January 2024
