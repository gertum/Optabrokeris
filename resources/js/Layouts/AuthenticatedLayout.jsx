import ApplicationLogo from '@/Components/ApplicationLogo';
import LanguageSwitch from '@/Components/LanguageSwitch';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import { Link } from '@inertiajs/react';
import { Avatar, Col, Row, Space, Layout } from 'antd'; // Make sure you import Menu from Ant Design
import { useTranslation } from 'react-i18next';

const { Header } = Layout;

export default function Authenticated({ user, header, children }) {
  const { t } = useTranslation();
  const isDashboardActive =
    route().current('/') || route().current('dashboard');

  return (
    <div className="min-h-screen bg-blue-200">
      <div className="bg-white border-b border-gray-100">
        <Header className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-white">
          <Row>
            <Col span={8}>
              <Space size={12}>
                <Link href="/">
                  <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                </Link>
                {/*<NavLink href={route('dashboard')} active={isDashboardActive}>*/}
                {/*    {t('dashboard')}*/}
                {/*</NavLink>*/}
                <NavLink
                  href={route('jobs.list')}
                  active={route().current('jobs.list')}
                >
                  {t('profiles')}
                </NavLink>
                <NavLink
                  href={route('jobs.new')}
                  active={route().current('jobs.new')}
                >
                  {t('newProfile')}
                </NavLink>
              </Space>
            </Col>
            <Col span={16} style={{ textAlign: 'right' }}>
              <Space size={12}>
                <Dropdown>
                  <Dropdown.Trigger>
                    <Avatar className="bg-blue-300 text-bold" size="large">
                      {user.name.slice(0, 1).toUpperCase()}
                    </Avatar>
                  </Dropdown.Trigger>

                  <Dropdown.Content>
                    <Dropdown.Link href={route('profile.edit')}>
                      Profile
                    </Dropdown.Link>
                    <Dropdown.Link
                      href={route('logout')}
                      method="post"
                      as="button"
                    >
                      Log Out
                    </Dropdown.Link>
                    <div
                      className="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                      style={{ borderTop: '1px solid #f0f0f0' }}
                    >
                      <LanguageSwitch />
                    </div>
                  </Dropdown.Content>
                </Dropdown>
              </Space>
            </Col>
          </Row>
        </Header>
      </div>

      {header && (
        <header className="bg-white shadow">
          <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center">
            {header}
          </div>
        </header>
      )}

      <main>{children}</main>
    </div>
  );
}
