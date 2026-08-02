import { NextResponse } from 'next/server';

export async function POST(request: Request) {
  try {
    const { username, password } = await request.json();

    const expectedUser = process.env.ADMIN_USER || 'clasnet';
    const expectedPassword = process.env.ADMIN_PASSWORD || 'Dikantor@5474';

    if (username === expectedUser && password === expectedPassword) {
      // Create a response and set an HTTP-only cookie
      const response = NextResponse.json({ success: true, message: 'Logged in successfully' });
      
      // We will set a simple cookie for authentication. In production, this should be a secure signed JWT.
      response.cookies.set('admin_logged_in', 'true', {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict',
        maxAge: 60 * 60 * 24 * 7, // 1 week
        path: '/',
      });

      return response;
    }

    return NextResponse.json({ success: false, error: 'Username atau password salah.' }, { status: 401 });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
