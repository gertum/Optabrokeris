import React from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function Guest({ children }) {
  return (
    <div className="min-h-screen flex flex-col sm:flex-row sm:justify-between bg-gray-100">
      <div className="w-2/5 bg-white flex flex-col items-center justify-center p-6">
        <Link href="/">
          <ApplicationLogo className="w-20 h-20 fill-current text-gray-500 mb-6" />
        </Link>
        {children}
      </div>

      <div className="hidden sm:block w-3/5 h-screen bg-blue-200"></div>
    </div>
  );
}
