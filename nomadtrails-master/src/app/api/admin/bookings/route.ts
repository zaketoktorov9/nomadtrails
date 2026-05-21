import { auth } from "@/auth";
import pool from "@/lib/db";
import { NextResponse } from "next/server";

export async function PATCH(req: Request) {
  try {
    const session = await auth();
    if (!session || (session.user as any).role !== 'admin') {
      return NextResponse.json({ error: "Unauthorized" }, { status: 403 });
    }

    const { bookingId, status } = await req.json();

    await pool.query(
      "UPDATE bookings SET status = ? WHERE id = ?",
      [status, bookingId]
    );

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("Error updating booking:", error);
    return NextResponse.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
