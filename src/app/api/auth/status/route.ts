import { NextResponse } from 'next/server';
import { cookies } from 'next/headers';

export async function GET() {
  const cookieStore = await cookies();
  const isLoggedIn = cookieStore.get('admin_logged_in')?.value === 'true';

  return NextResponse.json({ isLoggedIn });
}
