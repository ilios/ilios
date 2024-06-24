# Storing Learning Materials in and AWS S3 Bucket

By default Ilios will store all of your learning materials in the location configured by `file_system_storage_path`. For most environments this is a good default. If you wish to run Ilios inside a docker container, in a non-traditional hosting environment, or not worry about backing up this data, it may make sense to move your learning materials to AWS's S3 service.

## AWS Configuration

1) Create an S3 bucket in your preferred region.

    a) we recommend turning on object encryption.

    b) The should *not* be public or publicly accessible. Ilios will use the AWS API and a key you
    setup to access the learning material data.

2) Add an access policy with the correct permissions to your new S3 bucket:

    ```json
    {
        "Version": "2012-10-17",
        "Statement": [
            {
                "Sid": "VisualEditor0",
                "Effect": "Allow",
                "Action": [
                    "s3:ReplicateObject",
                    "s3:PutObject",
                    "s3:GetObjectAcl",
                    "s3:GetObject",
                    "s3:ListBucket",
                    "s3:DeleteObject",
                    "s3:PutObjectAcl"
                ],
                "Resource": [
                    "arn:aws:s3:::YOUR-BUCKET-NAME/*",
                    "arn:aws:s3:::YOUR-BUCKET-NAME"
                ]
            }
        ]
    }
    ```

    Replace `YOUR-BUCKET-NAME` with the S3 bucket you configured in step 1.

3) Create an AWS user with *only* this policy applied. *Do not* use your root or personal
account for this purpose.

    a) This is an API user and does not need console access
    b) Generate a token and note the *key* and *secret* from AWS, you will need these to configure Ilios.

## Ilios Configuration

1) Create a special s3 URL for your bucket. The format of this URL is `s3://KEY:SECRET@bucket.region`.
2) Tell Ilios about this URL by adding it to your configuration by running `$ sudo -u apache bin/console ilios:set-config-value storage_s3_url 'YOUR_S3_URL'`
