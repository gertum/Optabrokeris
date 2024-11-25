import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import {Layout, Button, Avatar, Card, message, Spin, Space, Divider} from 'antd';
import {
    EyeOutlined,
    ReloadOutlined,
    EyeInvisibleOutlined,
    DownloadOutlined,
} from '@ant-design/icons';
import { useTranslation } from 'react-i18next';
import { format, parseISO } from 'date-fns';
import axios from 'axios';
import { useEffect, useState } from 'react';

export default function List({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('jobs.createdJobs')}
                </h2>
            }
        >
            <Head title="Subjects" />
            <Content
                style={{
                    textAlign: 'center',
                    minHeight: 'calc(100vh - 128px)',
                    lineHeight: 4,
                }}
            >
                <div className="py-6">
                    <div className="mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 text-gray-900">
                                Test Subjects
                            </div>
                        </div>
                    </div>
                </div>
            </Content>
        </AuthenticatedLayout>
    );
}