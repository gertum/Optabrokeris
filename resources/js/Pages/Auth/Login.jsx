import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';

import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import LanguageSwitch from '../../Components/LanguageSwitch';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useState(() => {
    reset('password');
  }, []);

  const submit = e => {
    e.preventDefault();
    post(route('login'));
  };

  return (
    <GuestLayout>
      <Head title={t('login.title')} />

      {status && (
        <div className="mb-4 font-medium text-sm text-green-600">{status}</div>
      )}

      <form onSubmit={submit}>
        <div>
          <InputLabel htmlFor="email" value={t('login.email')} />

          <TextInput
            id="email"
            type="email"
            name="email"
            value={data.email}
            className="mt-1 block w-full"
            autoComplete="username"
            isFocused={true}
            onChange={e => setData('email', e.target.value)}
          />

          <InputError message={errors.email} className="mt-2" />
        </div>

        <div className="mt-4">
          <InputLabel htmlFor="password" value={t('login.password')} />

          <TextInput
            id="password"
            type="password"
            name="password"
            value={data.password}
            className="mt-1 block w-full"
            autoComplete="current-password"
            onChange={e => setData('password', e.target.value)}
          />

          <InputError message={errors.password} className="mt-2" />
        </div>

        <div>
          {canResetPassword && (
            <a
              href={route('password.request')}
              className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {t('login.forgotPassword')}
            </a>
          )}
          <Link
            href={route('register')}
            className="ml-4 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {t('login.newProfile')}
          </Link>
        </div>
        <div className="text-center mt-2">
          <PrimaryButton disabled={processing}>
            {t('login.logIn')}
          </PrimaryButton>
        </div>
      </form>

      <div className="mt-2">
        <LanguageSwitch />
      </div>
    </GuestLayout>
  );
}
