import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { Layout, Button, Avatar, Card, message } from 'antd';
import { EditOutlined, EyeOutlined, ReloadOutlined } from '@ant-design/icons';
import { useTranslation } from 'react-i18next';
import { format, parseISO } from 'date-fns';
import axios from 'axios';
import { useEffect, useState } from 'react';
import NewJob from '@/Pages/NewJob.jsx';

const { Content } = Layout;

export default function Jobs({ auth, jobs }) {
  const { t } = useTranslation();
  const [token, setToken] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const jobsPerPage = 6;
  const startIndex = (currentPage - 1) * jobsPerPage;
  const endIndex = startIndex + jobsPerPage;
  const displayedJobs = jobs.slice(startIndex, endIndex);

  const handleResolve = jobId => {
    axios
      .post(`/api/job/${jobId}/solve?_token=${token}`)
      .then(response => {
        console.log('handleSolve: ', response.data);
        message.success(`${jobId} resolved`, 5);
      })
      .catch(error => {
        message.error(`handleResolve error: ${error.message}`, 5);
      });
  };

  useEffect(() => {
    axios
      .get('/login')
      .then(response => {
        setToken(response.data);
      })
      .catch(error => {
        message.error(`Login error: ${error.message}`, 5);
      });
  }, []);

  return jobs.length ? (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          {t('jobs.createdJobs')}
        </h2>
      }
    >
      <Head title="Jobs" />
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
                {displayedJobs.map((job, index) => (
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
                            ? job.type[0].toUpperCase() + job.type.substring(1)
                            : '-'}
                        </Avatar>
                      </div>
                      <div className="job-info">
                        <div className="job-text">
                          <h3>
                            {job?.name && job?.id
                              ? job.name[0].toUpperCase() +
                                job.name.substring(1)
                              : 'No Name'}
                          </h3>
                          <p>
                            Created at:
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
                            href={route('jobs.edit', {
                              jobId: job.id,
                              jobType: job.type,
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
                            disabled={!job.data.includes('timeslotList')}
                            onClick={() =>
                              window.open(
                                `/api/job/${job.id}/download`,
                                '_blank'
                              )
                            }
                          >
                            Download
                          </Button>,
                          <Button
                            icon={<ReloadOutlined />}
                            size="large"
                            disabled={!job.data.includes('timeslotList')}
                            onClick={() => handleResolve(job.id)}
                          >
                            Rerun
                          </Button>,
                        ]}
                      </div>
                    </div>
                  </Card>
                ))}
                <Button
                  disabled={currentPage === 1}
                  onClick={() => setCurrentPage(currentPage - 1)}
                >
                  Previous
                </Button>
                <Button
                  disabled={endIndex >= jobs.length}
                  onClick={() => setCurrentPage(currentPage + 1)}
                >
                  Next
                </Button>
              </div>
            </div>
          </div>
        </div>
      </Content>
    </AuthenticatedLayout>
  ) : (
    <NewJob auth={auth} />
  );
}
