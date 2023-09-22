import React from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import Logo from '../../images/Logo.png';

export default function Guest({ children }) {
  return (
    <div className="min-h-screen flex flex-col sm:flex-row sm:justify-between bg-gray-100">
      <div className="w-2/5 bg-white flex flex-col items-center justify-center p-6">
        <Link href="/">
          <img src={Logo} alt="Logo" className="h-20" />
        </Link>
        {children}
      </div>

      <div className="hidden sm:block w-3/5 h-screen bg-blue-200"></div>
    </div>
  );
}
