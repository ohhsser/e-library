import React from "react";
import Zero from "../Assets/0.svg";
import Four from "../Assets/4.svg";
import Face from "../Assets/Face.png";
import { RiArrowGoBackFill } from "react-icons/ri";
import { useNavigate } from "react-router-dom";

const Error404 = () => {
  const navigate = useNavigate();
  return (
    <div className="bg-[#ff31311c] p-4 grid place-content-center">
      <div className="flex items-center gap-10 flex-col ">
        <div className="text-center ">
          <h1 className="text-5xl font-bold">Oh no!</h1>
          <p className="text-2xl font-bold text-gray-400">Page Not Found!</p>
        </div>
        <div className=" w-[100px] lg:w-full items-center justify-center flex lg:gap-12 gap-28 ">
          <img src={Four} className="" />
          <div className="lg:relative absolute w-[100px] lg:w-96 lg:justify-center lg:flex lg:top-0 lg:left-0">
            <img src={Face} className=" absolute top-[30px] " />
            <img className=" " src={Zero} />
          </div>
          <img src={Four} />
        </div>
        <button
          onClick={() => navigate(-1)}
          className="flex bg-white items-center gap-4 p-4 rounded-full px-8 border-2 border-black "
        >
          <RiArrowGoBackFill />
          Go Back
        </button>
      </div>
    </div>
  );
};

export default Error404;
