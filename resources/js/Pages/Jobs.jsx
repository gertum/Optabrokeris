import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Layout, Button } from "antd";
import {useTranslation} from "react-i18next";

const { Content } = Layout;

export default function Jobs({ auth, jobs }) {
    const { t } = useTranslation();

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{t('jobs.createdProfiles')}</h2>}
        >
            <Head title="Jobs" />
            {jobs.length ? jobs : <Content style={{
                textAlign: 'center',
                minHeight: 'calc(100vh - 128px)',
                lineHeight: 4,
            }}>
                <div>{t('jobs.noProfiles')}</div>
                <Link href={route('jobs.new')}>
                    <Button shape="circle" style={{ height: 100, width: 100, display: 'inline-block' }}>
                        <span style={{ whiteSpace: 'normal', textAlign: 'center' }}>{t('jobs.createNewProfile')}</span>
                    </Button>
                </Link>
            </Content>}
        </AuthenticatedLayout>
    );
}