import { Button } from 'antd';
import { Link } from '@inertiajs/react';

export const FinalForm = ({ updated = false }) => {
  return (
    <div className="my-2">
      <h2 className="font-semibold text-xl text-gray-800 leading-tight">
        {updated ? 'Succesfully updated' : 'Succesfully created!!!'}
      </h2>
      <Link href="/jobs" className="mt-2">
        <Button size="large">View Profiles List</Button>
      </Link>
    </div>
  );
};
