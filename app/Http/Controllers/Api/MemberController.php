<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:17
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\MemberRequest;
use App\Http\Requests\MembershipFormRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\SmartMicro\Repositories\Contracts\AccountInterface;
use App\SmartMicro\Repositories\Contracts\MemberInterface;

use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController  extends ApiController
{
    /**
     * @var MemberInterface
     */
    protected $memberRepository, $accountRepository, $load;

    /**
     * MemberController constructor.
     * @param MemberInterface $memberInterface
     * @param AccountInterface $accountInterface
     */
    public function __construct(MemberInterface $memberInterface, AccountInterface $accountInterface)
    {
        $this->memberRepository   = $memberInterface;
        $this->accountRepository   = $accountInterface;
        $this->load = ['branch', 'assets', 'account', 'guaranteedLoans'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
       // return Member::withoutGlobalScopes()->get();

        if ($select = request()->query('list')) {
           // return $this->memberRepository->listAll($this->formatFields($select));
            return $this->memberRepository->listAll($this->formatFields($select), ['account']);

        } else
            $data = MemberResource::collection($this->memberRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param MemberRequest $request
     * @return mixed
     */
    public function store(MemberRequest $request)
    {
        $data = $request->all();

        // Upload passport photo
        $data['passport_photo'] = null;
        if($request->hasFile('passport_photo')) {
          // return $this->respondWithData($data);
            // Get filename with extension
            $filenameWithExt = $request->file('passport_photo')->getClientOriginalName();

            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get just ext
            $extension = $request->file('passport_photo')->getClientOriginalExtension();

            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            // Upload Image
            // $path = $request->file('attach_application_form')->storeAs('public/cover_images', $fileNameToStore);
            $path = $request->file('passport_photo')->storeAs('members', $fileNameToStore);

            $data['passport_photo'] = $fileNameToStore;
        }

        // Upload membership form
        $data['membership_form'] = null;
        if($request->hasFile('membership_form')) {
            // return $this->respondWithData($data);
            // Get filename with extension
            $filenameWithExt = $request->file('membership_form')->getClientOriginalName();

            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get just ext
            $extension = $request->file('membership_form')->getClientOriginalExtension();

            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            // Upload
            $path = $request->file('membership_form')->storeAs('membership_forms', $fileNameToStore);

            $data['membership_form'] = $fileNameToStore;
        }

        $save = $this->memberRepository->create($data);

        if($save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            // New member email / sms
          //  CommunicationMessage::send('new_member_welcome', $save, $save);
            return $this->respondWithSuccess('Success !! Member has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $member = $this->memberRepository->getById($uuid);

        if(!$member)
        {
            return $this->respondNotFound('Member not found.');
        }
        return $this->respondWithData(new MemberResource($member));

    }

    /**
     * @param MemberRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(MemberRequest $request, $uuid)
    {
        $save = $this->memberRepository->update($request->all(), $uuid);

        if($save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! Member has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->memberRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! Member has been deleted');
        }
        return $this->respondNotFound('Member not deleted');
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function profilePic(Request $request)
    {
        $data = $request->all();

        if( array_key_exists('file_path', $data) ) {
            $file_path = $data['file_path'];

         /*   if (!Storage::disk('local')->exists($file_path)) {
                return $this->respondNotFound('Image not found');
            }*/

            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'members'.DIRECTORY_SEPARATOR. $file_path;

            return response()->file($local_path);
        }

        return $this->respondNotFound('file_path not provided');
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function membershipForm(Request $request)
    {
        $data = $request->all();
        // return $data;
        if( array_key_exists('file_path', $data) ) {
            $file_path = $data['file_path'];
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'membership_forms'.DIRECTORY_SEPARATOR. $file_path;
            return response()->file($local_path);
        }
        return $this->respondNotFound('file_path not provided');
    }

    /**
     * @param MembershipFormRequest $request
     */
    public function updateMembershipForm(MembershipFormRequest $request) {
        $data = $request->all();
        // Upload
        if($request->hasFile('membership_form')) {
            $filenameWithExt = $request->file('membership_form')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('membership_form')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('membership_form')->storeAs('membership_forms', $fileNameToStore);
            // $data['logo'] = $fileNameToStore;
            $data['membership_form'] = $fileNameToStore;
        }
        // TODO also, delete previous image file from server
        $this->memberRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fetchPhoto(Request $request)
    {
        $data = $request->all();

        $file_path = $data['file_path'];
        if( array_key_exists('file_path', $data) && $file_path == null ) {
            $file_path = $data['file_path'];
        }
        $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'members'.DIRECTORY_SEPARATOR. $file_path;
        return response()->file($local_path);
    }

    /**
     * @param Request $request
     */
    public function updatePhoto(Request $request) {
        $data = $request->all();
        // Upload logo
        if($request->hasFile('passport_photo')) {
            $filenameWithExt = $request->file('passport_photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('passport_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('passport_photo')->storeAs('members', $fileNameToStore);
            // $data['logo'] = $fileNameToStore;
            $data['passport_photo'] = $fileNameToStore;
        }
        // TODO also, delete previous image file from server
        $this->memberRepository->update(array_filter($data), $data['id']);
    }
}