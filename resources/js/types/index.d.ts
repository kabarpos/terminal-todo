export interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    avatar_url: string | null;
    roles: string[];
    permissions: string[];
    status: 'pending' | 'active' | 'rejected' | 'banned' | 'inactive';
    email_verified_at: string | null;
    last_login_at: string | null;
}

export interface Auth {
    user: User | null;
}

export interface AppData {
    name: string;
    env: string;
    url: string;
}

export interface FlashData {
    message?: string | null;
    error?: string | null;
    success?: string | null;
    warning?: string | null;
}

export interface PageProps {
    auth: Auth;
    app: AppData;
    flash: FlashData;
}

// Layout Props
export interface BaseLayoutProps extends PageProps {
    title: string;
    description?: string;
}

// Form Types
export interface ProfileUpdateForm {
    name: string;
    email: string;
    phone: string;
    avatar: File | null;
    _method: string;
}

export interface PasswordUpdateForm {
    current_password: string;
    password: string;
    password_confirmation: string;
}

// Response Types
export interface ApiResponse<T = any> {
    data: T;
    message?: string;
    errors?: Record<string, string[]>;
} 