import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import { Link } from '@inertiajs/react';
import {Avatar, Col, Row, Space, Layout} from "antd";

const { Header } = Layout;

export default function Authenticated({ user, header, children }) {
    const isDashboardActive = route().current('/') || route().current('dashboard');

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="bg-white border-b border-gray-100">
                <Header className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-white">
                    <Row>
                        <Col span={6}>
                            <Space size={12}>
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                                <NavLink href={route('dashboard')} active={isDashboardActive}>
                                    Dashboard
                                </NavLink>
                                <NavLink href={route('jobs.list')} active={route().current('jobs.list')}>
                                    Profiles
                                </NavLink>
                                <NavLink href={route('jobs.new')} active={route().current('jobs.new')}>
                                    New profile
                                </NavLink>
                            </Space>
                        </Col>
                        <Col span={18} style={{textAlign: 'right'}}>
                            <Space size={12}>
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <Avatar size="large">{user.name.slice(0, 1).toUpperCase()}</Avatar>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                                        <Dropdown.Link href={route('logout')} method="post" as="button">
                                            Log Out
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </Space>
                        </Col>
                    </Row>
                </Header>
            </div>

            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center">{header}</div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
