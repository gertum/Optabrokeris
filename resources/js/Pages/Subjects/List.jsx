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

const { Content } = Layout;

export default function List({ auth }) {
    const { t } = useTranslation();
    const [subjects, setSubjects] = useState([]);
    const [token, setToken] = useState('');

    const fetchSubjects = async () => {
        try {
            // setLoadingJobs(true);
            const subjectsResponse = await axios.get('/api/subject');
            setSubjects(subjectsResponse.data);
        } catch (error) {
            // setErrorJobs(error.message);
            message.error(`Subjects load error: ${error.message}`, 5);

        } finally {
            // setLoadingJobs(false);
        }
    };

    const fetchToken = async () => {
        try {
            const tokenResponse = await axios.get('/login');
            setToken(tokenResponse.data);
        } catch (error) {
            message.error(`Login error: ${error.message}`, 5);
        }
    };

    useEffect(() => {
        fetchToken();
        fetchSubjects();
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {t('subjects.list')}
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
                                <p>Test Subjects</p>

                                {subjects.map((subject, index) => (
                                    <div className="job-info">
                                        <div className="job-text">
                                            <p> {subject.name}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </Content>
        </AuthenticatedLayout>
    );
}