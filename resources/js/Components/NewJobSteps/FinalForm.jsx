export const FinalForm = ({ token, jobId,created = false, children }) => {
  return (
    <div className="my-2">
      <p>Solution is ready. To download result <a href={`/api/job/${jobId}/download?_token=${token}`}>press here.</a></p>
      {children}
    </div>
  );
};
