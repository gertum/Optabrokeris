import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Layout, Button, Avatar, Card } from 'antd';
import { EditOutlined, EyeOutlined, ReloadOutlined } from '@ant-design/icons';
import { useTranslation } from 'react-i18next';
import { format, parseISO } from 'date-fns';

const { Content } = Layout;

export default function Jobs({ auth, jobs }) {
  const { t } = useTranslation();

  const handleResolve = jobId => {
    console.log(jobId);
  };

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          {t('jobs.createdProfiles')}
        </h2>
      }
    >
      <Head title="Jobs" />
      {jobs.length ? (
        <Content
          style={{
            textAlign: 'center',
            minHeight: 'calc(100vh - 128px)',
            lineHeight: 4,
          }}
        >
          <div className="py-12">
            <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 text-gray-900">
                  {jobs.map((job, index) => (
                    <Card
                      key={index}
                      className={`${jobs.length - 1 !== index ? 'mb-4' : ''}`}
                    >
                      <div
                        style={{
                          display: 'flex',
                          justifyContent: 'space-between',
                          alignItems: 'center',
                        }}
                      >
                        <div className="job-icon">
                          <Avatar className="bg-blue-500 text-bold" size={64}>
                            {job?.type
                              ? job.type[0].toUpperCase() +
                                job.type.substring(1)
                              : '-'}
                          </Avatar>
                        </div>
                        <div className="job-info">
                          <div className="job-text">
                            <h3>
                              {job?.type
                                ? job.type[0].toUpperCase() +
                                  job.type.substring(1)
                                : '-'}
                            </h3>
                            <p>
                              Created at:{' '}
                              {job?.created_at
                                ? format(
                                    parseISO(job.created_at),
                                    'yyyy-MM-dd HH:mm:ss'
                                  )
                                : '-'}
                            </p>
                          </div>
                        </div>
                        <div className="job-actions">
                          {[
                            <Link
                              href={route('jobs.new', {
                                jobTitle: job.title,
                                createdAt: job.createdAt,
                              })}
                              className="ant-btn ant-btn-lg"
                            >
                              <Button icon={<EditOutlined />} size="large">
                                Edit
                              </Button>
                            </Link>,
                            <Button
                              icon={<EyeOutlined />}
                              size="large"
                              onClick={() =>
                                window.open(`/api/job/${job.id}`, '_blank')
                              }
                            >
                              Download
                            </Button>,
                            <Button
                              icon={<ReloadOutlined />}
                              size="large"
                              onClick={() => handleResolve(job.id)}
                            >
                              Rerun
                            </Button>,
                          ]}
                        </div>
                      </div>
                    </Card>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </Content>
      ) : (
        <Content
          style={{
            textAlign: 'center',
            minHeight: 'calc(100vh - 128px)',
            lineHeight: 4,
          }}
        >
          <div>{t('jobs.noProfiles')}</div>
          <Link href={route('jobs.new')}>
            <Button
              shape="circle"
              style={{
                height: 100,
                width: 100,
                display: 'inline-block',
                border: 'solid',
              }}
            >
              <span style={{ whiteSpace: 'normal', textAlign: 'center' }}>
                {t('jobs.createNewProfile')}
              </span>
            </Button>
          </Link>
        </Content>
      )}
    </AuthenticatedLayout>
  );
}
