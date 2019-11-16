import ReactDOM from 'react-dom';

export function reloadPage() {
    return location.reload();
}

export function redirectToLogin() {
    return location.href = 'login/github';
}
