import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link} from '@inertiajs/react';

export default function Jobs({ auth, jobs }) {
    const jobsHtml = jobs.map((job) =>
        <tr>
            <td>{job.id}</td>
            <td>{job.type}</td>
            <td>{job.status}</td>
            <td>{job.data}</td>
            <td>{job.result}</td>
            <td>
                <a href={"/jobs/10"}>View</a>
            </td>
        </tr>
    );
    // console.log('jobs', jobs);

    return (
        <>
            <Head title="Jobs" />

            <div>
                <table>
                    <thead></thead>
                        <tr>
                            <td>id</td>
                            <td>type</td>
                            <td>status</td>
                            <td>data</td>
                            <td>result</td>
                            <td>Actions</td>
                        </tr>
                    <tbody>
                        {jobsHtml}
                    </tbody>
                </table>
            </div>
        </>
    );
}