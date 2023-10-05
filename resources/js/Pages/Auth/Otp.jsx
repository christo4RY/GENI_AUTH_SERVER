import React from "react";
import { useForm } from "@mantine/form";
import { router } from "@inertiajs/react";
import { toast } from 'react-toastify';
import VerificationInput from "react-verification-input";
import axios from "axios";
import "./Otp.css"

const Otp = () => {
    const form = useForm({
        initialValues: { otp: "" },
    });

    const verify = async (e) => {
        const {data} = await axios.post(route('verify.otp'),form.values)
        if(data.status === 200){
            toast.success(data.msg)
            location.href = data.redirect_url
            // router.get(route('verify.otp'))
        }else{
            toast.error(data.msg)
        }
    }
    return (
        <div className="h-screen flex flex-col md:flex-row">
            <div className="w-full md:w-[50%] h-full bg-[#053B50]">
                <img src="/web/images/login.jpg" className="w-full h-full object-cover" alt="" />
            </div>
            <div className="w-full md:w-[50%] flex items-center">
                <div className="w-full">
                    <div className="w-full md:w-[70%] mx-auto p-8 md:p-10">
                        <div className="mb-8 w-full items-center flex flex-col justify-center space-y-4">
                            <div>
                                <img src="data:image/png;base64,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"></img>
                            </div>
                            <h1 className="text-3xl font-semibold">OTP Verification</h1>
                            <h5 className="text-center text-slate-400 text-sm">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim
                                aspernatur maxime sed quas.
                            </h5>
                        </div>
                        <form onSubmit={form.onSubmit(verify)} className="w-full flex flex-col items-center justify-center">
                            <VerificationInput classNames="w-full" {...form.getInputProps('otp')} placeholder="_" validChars="0-9" inputProps={{ inputMode: "numeric", autoComplete: "one-time-code" }} />
                            <button className="md:py-2 md:w-[75%] mt-5 w-full text-white px-5 bg-[#A1C4FF]">click to verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Otp;
