import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Dashboard
        </h2>
      }
    >
      <Head title="Main" />

      <div className="py-12">
        <div className="mx-auto sm:px-6 lg:px-8">
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">
              <h3>
                Welcome to EfficientPlan - Your Ultimate Planning Companion
              </h3>

              <p>
                At EfficientPlan, we believe that successful outcomes begin with
                effective planning. Whether you're a busy school teacher, a
                meticulous route planner, a project manager juggling tasks, or
                an HR manager handling employee schedules, we've got your
                planning needs covered. Our comprehensive platform is designed
                to simplify and streamline your planning processes, ensuring you
                achieve your goals with ease and precision.
              </p>

              <h4>Our Services</h4>
              <ul>
                <li>
                  <strong>Route Planning</strong>: Take the guesswork out of
                  travel...
                </li>
                <li>
                  <strong>School Teacher's Work Schedules</strong>: For
                  educators dedicated to shaping young minds...
                </li>
                <li>
                  <strong>Task Assignments</strong>: Stay on top of your to-do
                  list...
                </li>
                <li>
                  <strong>Employee Rostering</strong>: Managing a workforce is a
                  breeze...
                </li>
              </ul>

              <h4>Why Choose EfficientPlan?</h4>
              <ul>
                <li>
                  <strong>User-Friendly Interface</strong>: Our platform boasts
                  an intuitive design...
                </li>
                <li>
                  <strong>Customization</strong>: We understand that each
                  planning scenario is unique...
                </li>
                <li>
                  <strong>Collaboration Made Easy</strong>: Collaborate
                  seamlessly with colleagues...
                </li>
                <li>
                  <strong>Real-Time Updates</strong>: Stay informed with
                  real-time updates and notifications...
                </li>
                <li>
                  <strong>Data Security</strong>: We take your data seriously...
                </li>
              </ul>

              <p>
                <strong>Get Started Today</strong>
              </p>
              <p>
                Join the countless professionals who have revolutionized their
                planning with EfficientPlan. Whether you're charting out a road
                trip, orchestrating classroom activities, managing tasks, or
                creating employee schedules, our platform empowers you to plan
                with precision and achieve exceptional results.
              </p>

              <p>
                Start your journey towards efficient planning today. Sign up for
                EfficientPlan and unlock a world of organized possibilities.
                Your success begins with smart planning, and we're here to make
                that journey smooth and rewarding.
              </p>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
