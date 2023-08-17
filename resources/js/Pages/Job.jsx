import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link} from '@inertiajs/react';
import {Layout} from "antd";

const { Content } = Layout;

export default function Job({ auth, job }) {

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>}
        >
        <Head title="View Job" />
            {job ? job : <Content style={{
                textAlign: 'center',
                minHeight: 'calc(100vh - 128px)',
                lineHeight: 4,
            }}>
                <div>Profile does not exist</div>
            </Content>}
        </AuthenticatedLayout>
    );
}