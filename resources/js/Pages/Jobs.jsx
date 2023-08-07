import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Layout, Button } from "antd";

const { Content } = Layout;

export default function Jobs({ auth, jobs }) {

    console.log('--------------------------')
    console.log('jobs')
    console.log(jobs)
    console.log('--------------------------')

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Created profiles</h2>}
        >
            <Head title="Jobs" />
            {jobs.length ? jobs : <Content style={{
                textAlign: 'center',
                minHeight: 'calc(100vh - 128px)',
                lineHeight: 4,
            }}>
                <div>You have no profiles yet, please start by creating new profile</div>
                <Link href={route('jobs.new')}>
                    <Button shape="circle" style={{ height: 100, width: 100, display: 'inline-block' }}>
                        <span style={{ whiteSpace: 'normal', textAlign: 'center' }}>Create new Profile</span>
                    </Button>
                </Link>
            </Content>}
        </AuthenticatedLayout>
    );
}