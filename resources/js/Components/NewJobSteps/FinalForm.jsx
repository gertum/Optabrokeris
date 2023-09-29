export const FinalForm = ({ created = false, children }) => {
  return (
    <div className="my-2">
      <h2 className="font-semibold text-xl text-gray-800 leading-tight">
        {created ? 'Succesfully created!!!' : 'Check your whole list:'}
      </h2>
      {children}
    </div>
  );
};
