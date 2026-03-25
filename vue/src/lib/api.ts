const BASE_URL = import.meta.env.VITE_API_URL

export async function apiFetch(endpoint: string, options: RequestInit = {}): Promise<Response> {
  const token = localStorage.getItem('token')

  return fetch(`${BASE_URL}${endpoint}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
      ...options.headers
    }
  })
}

export function logout() {
  localStorage.removeItem('token')
  window.location.href = '/login'
}